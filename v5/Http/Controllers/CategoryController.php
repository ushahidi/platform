<?php

namespace v5\Http\Controllers;

use Illuminate\Auth\Access\Gate;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;
use Ushahidi\App\Validator\LegacyValidator;
use v5\Http\Resources\CategoryCollection;
use v5\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use v5\Models\Category;
use v5\Models\Translation;
use Illuminate\Support\Facades\DB;

class CategoryController extends V5Controller
{

    /**
     * Not all fields are things we want to allow on the body of requests
     * an author won't change after the fact so we limit that change
     * to avoid issues from the frontend.
     * @return string[]
     */
    protected function ignoreInput()
    {
        return ['author_email', 'slug', 'user_id', 'author_realname', 'created', 'updated'];
    }

    private function runAuthorizer($ability, $object)
    {
        $authorizer = service('authorizer.form');
        // if there's no user the guards will kick them off already, but if there
        // is one we need to check the authorizer to ensure we don't let
        // users without admin perms create forms etc
        // this is an unfortunate problem with using an old version of lumen
        // that doesn't let me do guest user checks without adding more risk.
        $user = $authorizer->getUser();
        if ($user) {
            $this->authorize($ability, $object);
        }
        return $user;
    }

    private function setInputDefaults($input, $action)
    {
        if ($action === 'store') {
            $input['slug'] = Category::makeSlug($input['slug'] ?? $input['tag']);
        }
        return $input;
    }
    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $category = Category::find($id);
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
        return new CategoryCollection(Category::get());
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
        $input = $this->getFields($request->input());
        if (empty($input)) {
            return self::make500('POST body cannot be empty');
        }
        $this->runAuthorizer('store', Category::class);

        $input = $this->setInputDefaults($input, 'store');

        $category = new Category();
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
                $category,
                $category->toArray(),
                $request->input('translations') ?? [],
                $category->id,
                'category'
            );
            if (!empty($errors)) {
                DB::rollback();
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
        $category = Category::withoutGlobalScopes()->find($id);

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
                new Category(),
                $category->toArray(),
                $request->input('translations') ?? [],
                $category->id,
                'category'
            );
            if (!empty($errors)) {
                DB::rollback();
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
     * @param Category $category
     * @param array $entity_array
     * @param array $translations
     * @return array
     */
    public function validateTranslations($category, $entity_array, array $translations)
    {
        $entity_array = array_merge($entity_array, $translations);
        if (isset($entity_array['slug'])) {
            $entity_array['slug'] = Category::makeSlug($entity_array['slug']);
        }
        if (!$category->validate($entity_array)) {
            return $category->errors->toArray();
        }
        return [];
    }

    /**
     * @param integer $id
     */
    public function delete(int $id, Request $request)
    {
        $category = Category::withoutGlobalScopes()->find($id);
        if (!$category) {
            return self::make404();
        }
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
