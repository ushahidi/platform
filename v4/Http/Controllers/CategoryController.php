<?php

namespace v4\Http\Controllers;

use Illuminate\Auth\Access\Gate;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;
use Ushahidi\App\Validator\LegacyValidator;
use v4\Http\Resources\CategoryCollection;
use v4\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use v4\Models\Category;
use v4\Models\Translation;
use Illuminate\Support\Facades\DB;

class CategoryController extends V4Controller
{
    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $category = Category::allowed()->with('translations')->find($id);
        if (!$category) {
            return self::make404();
        }
        return new CategoryResource($category);
    }//end show()

    /**
     * Display the specified resource.
     *
     * @return CategoryCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        return new CategoryCollection(Category::allowed()->get());
    }//end index()


    /**
     * Display the specified resource.
     *
     * @TODO   transactions =)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|CategoryResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $authorizer = service('authorizer.form');
        // if there's no user the guards will kick them off already, but if there
        // is one we need to check the authorizer to ensure we don't let
        // users without admin perms create forms etc
        // this is an unfortunate problem with using an old version of lumen
        // that doesn't let me do guest user checks without adding more risk.
        $user = $authorizer->getUser();
        if ($user) {
            $this->authorize('store', Category::class);
        }
        $input = $request->input();
        if (empty($input)) {
            return self::make500('POST body cannot be empty');
        }
        $input['slug'] = Category::makeSlug($input['slug'] ?? $input['tag']);
        $category = new Category();
        $id = null;
        if (!$category->validate($input)) {
            return self::make422($category->errors);
        }
        DB::beginTransaction();
        try {
            $category = Category::create(
                array_merge(
                    $input,
                    [
                        'created' => time(),
                    ]
                )
            );
            $errors = $this->saveTranslations(
                $category->toArray(),
                $request->input('translations') ?? [],
                $category->id,
                'category'
            );
            if (!empty($errors)) {
                return self::make422($errors, 'translation');
            }
            DB::commit();
            return new CategoryResource($category);
        } catch (\Exception $e) {
            DB::rollback();
            return self::make500($e->getMessage());
        }
    }//end store()


    /**
     * Display the specified resource.
     *
     * @TODO   transactions =)
     * @param integer $id
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(int $id, Request $request)
    {
        $category = Category::find($id);

        if (!$category) {
            return self::make404();
        }
        $this->authorize('update', $category);

        $input = $request->input();

        if (empty($input)) {
            return self::make500('POST body cannot be empty');
        }

        if (!$category->validate($input)) {
            return self::make422($category->errors);
        }

        DB::beginTransaction();
        try {
            $category->update($request->input());

            $errors = $this->updateTranslations(
                $category->toArray(),
                $request->input('translations') ?? [],
                $category->id,
                'category'
            );
            if (!empty($errors)) {
                return self::make422($errors, 'translation');
            }
            DB::commit();
            return new CategoryResource($category);
        } catch (\Exception $e) {
            DB::rollback();
            return self::make500($e->getMessage());
        }
    }//end update()


    /**
     * @param array $entity_array
     * @param array $translation_input
     * @param int $translatable_id
     * @param string $type
     * @return array
     */
    private function saveTranslations(array $entity_array, array $translation_input, int $translatable_id, string $type)
    {
        if (!is_array($translation_input)) {
            return [];
        }
        $category = new Category();
        $errors = [];
        foreach ($translation_input as $language => $translations) {
            $validation_errors = $this->validateTranslations($category, $entity_array, $translations);
            if (!empty($validation_errors)) {
                $errors[$language] = $validation_errors;
                continue;
            }
            foreach ($translations as $key => $translated) {
                if (is_array($translated)) {
                    $translated = json_encode($translated);
                }

                Translation::create(
                    [
                        'translatable_type' => $type,
                        'translatable_id'   => $translatable_id,
                        'translated_key'    => $key,
                        'translation'       => $translated,
                        'language'          => $language,
                    ]
                );
            }
        }
        return $errors;
    }//end saveTranslations()

    /**
     * @param array $entity_array
     * @param array $translation_input
     */
    private function validateTranslations(Category $category, $entity_array, array $translations)
    {
        $entity_array = array_merge($entity_array, $translations);
        $entity_array['slug'] = Category::makeSlug($entity_array['slug']);
        if (!$category->validate($entity_array)) {
            return $category->errors->toArray();
        }
        return [];
    }

    /**
     * @param array $entity_array
     * @param array $translation_input
     * @param int $translatable_id
     * @param string $type
     * @return array
     */
    private function updateTranslations(
        array $entity_array,
        array $translation_input,
        int $translatable_id,
        string $type
    ) {
        if (!is_array($translation_input)) {
            return [];
        }
        Translation::where('translatable_id', $translatable_id)->where('translatable_type', $type)->delete();
        return $this->saveTranslations($entity_array, $translation_input, $translatable_id, $type);
    }//end updateTranslations()


    /**
     * @param integer $id
     */
    public function delete(int $id, Request $request)
    {
        $category = Category::find($id);
        $this->authorize('delete', $category);
        $success = DB::transaction(function () use ($id, $request, $category) {
            $category->translations()->delete();
            $success = $category->delete();
            return $success;
        });
        if ($success) {
            return response()->json(['result' => ['deleted' => $id]]);
        } else {
            return self::make500('Could not delete model');
        }
    }//end delete()
}//end class
