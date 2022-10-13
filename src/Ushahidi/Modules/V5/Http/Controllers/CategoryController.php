<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ushahidi\Modules\V5\Models\Category;
use Ushahidi\Modules\V5\Http\Requests\CategoryRequest;
use Ushahidi\Modules\V5\Http\Resources\CategoryResource;
use Ushahidi\Modules\V5\Http\Resources\CategoryCollection;

class CategoryController extends V5Controller
{
    /**
     * Display the specified resource.
     *
     * @return CategoryCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        return new CategoryCollection(Category::get());
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
    }

    /**
     * Display the specified resource.
     *
     * @TODO   transactions =)
     *
     * @return \Illuminate\Http\JsonResponse|CategoryResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CategoryRequest $request)
    {
        $this->authorize('create', Category::class);

        $input = $this->getFields($request->all());

        DB::beginTransaction();
        try {
            $category = Category::create(
                array_merge($input, ['created' => time()])
            );
            $errors = $this->saveTranslations(
                $category,
                $category->toArray(),
                $request->input('translations', []),
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
    }

    /**
     * Display the specified resource.
     *
     * @TODO   transactions =)
     * @param integer $id
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(CategoryRequest $request)
    {
        // Doing this so tests can pass, apparently the test suite
        // assumes finding the resource before validation.
        // This is achievable if we use route model binding.
        $category = $request->category;

        $this->authorize('update', $category);

        DB::beginTransaction();
        try {
            $category->update($request->validated());
            $errors = $this->updateTranslations(
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
    }

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
            $entity_array['slug'] = $category::makeSlug($entity_array['slug']);
        }

        $request = new CategoryRequest;

        if (($validator = Validator::make(
                $entity_array,
                $request->rules($entity_array),
                $request->messages()
            ))
            && $validator->fails()
        ) {
            return $validator->errors()->toArray();
        }

        return [];
    }

    /**
     * @param integer $id
     */
    public function delete(int $id)
    {
        $category = Category::withoutGlobalScopes()->find($id);
        if (!$category) {
            return self::make404();
        }

        $this->authorize('delete', $category);

        $success = DB::transaction(function () use ($category) {
            $category->translations()->delete();
            $success = $category->delete();
            return $success;
        });

        if ($success) {
            return response()->json(['result' => ['deleted' => $id]]);
        } else {
            return self::make500('Could not delete model');
        }
    }

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
}
