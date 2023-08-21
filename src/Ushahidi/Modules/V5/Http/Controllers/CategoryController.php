<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Ushahidi\Modules\V5\DTO\Paging;
use Symfony\Component\HttpFoundation\Response;
use Ushahidi\Modules\V5\Actions\Category\Commands\DeleteCategoryCommand;
use Ushahidi\Modules\V5\Actions\Category\Commands\StoreCategoryCommand;
use Ushahidi\Modules\V5\Actions\Category\Commands\UpdateCategoryCommand;
use Ushahidi\Modules\V5\Http\Resources\CategoryCollection;
use Ushahidi\Modules\V5\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Actions\Category\Queries\FetchAllCategoriesQuery;
use Ushahidi\Modules\V5\Actions\Category\Queries\FetchCategoryByIdQuery;
use Ushahidi\Modules\V5\Requests\CategoryRequest;
use Ushahidi\Modules\V5\Models\Category;
use Ushahidi\Modules\V5\Http\Requests\CategoryRequest as ValidationCategoryRequest;
use Ushahidi\Modules\V5\DTO\CategorySearchFields;

class CategoryController extends V5Controller
{
    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function show(int $id): CategoryResource
    {

        $category = $this->queryBus->handle(new FetchCategoryByIdQuery($id));
       // $this->authorize('show', $category);

        return new CategoryResource($category);
    }


    /**
     * Display the specified resource.
     *
     * @return CategoryCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
       // $this->authorize('index', new Category());

        return new CategoryCollection(
            $this->queryBus->handle(
                new FetchAllCategoriesQuery(
                    new Paging($request, 'id', Paging::ORDER_ASC, 0),
                    new CategorySearchFields($request)
                )
            )
        );
    }
    /**
     * Display the specified resource.
     *
     * @param CategoryRequest $request
     * @return ResponseFactory|Application|JsonResponse|Response
     * @throws AuthorizationException
     */
    public function store(CategoryRequest $request)
    {
        $this->authorize('create', Category::class);

        DB::beginTransaction();
        try {
            $id = $this->commandBus->handle(StoreCategoryCommand::createFromRequest($request));

            $category = $this->queryBus->handle(new FetchCategoryByIdQuery($id));
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
     * @param integer $id
     * @param Request $request
     * @return mixed
     * @throws AuthorizationException
     */
    public function update(int $id, CategoryRequest $request)
    {
        $category = $this->queryBus->handle(new FetchCategoryByIdQuery($id));
        $this->authorize('update', $category);
        DB::beginTransaction();
        try {
            $command = UpdateCategoryCommand::fromRequest($id, $request, $category);
            $category = $this->commandBus->handle($command);
            $errors = $this->updateTranslations(
                new Category(),
                $category->toArray(),
                $request->input('translations') ?? [],
                $category->id,
                'category'
            );
            if (!empty($errors)) {
                DB::rollback();
                // To do : change the return results to Exception
                return self::make422($errors, 'translation');
            }
             DB::commit();
             return new CategoryResource($category);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
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

        $request = new ValidationCategoryRequest;

        if (($validator = $this->getValidationFactory()->make(
            $entity_array,
            $request->rules($entity_array),
            $request->messages()
        )
            )
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

        // try {
        $category = $this->queryBus->handle(new FetchCategoryByIdQuery($id));
        $this->authorize('delete', $category);

     // $success = DB::transaction(function () use ($category) {
        //     $category->translations()->delete();
        //     $success = $category->delete();
        //     return $success;
        // });

        $this->commandBus->handle(new DeleteCategoryCommand($id));
        return $this->deleteResponse($id);
    }
}
