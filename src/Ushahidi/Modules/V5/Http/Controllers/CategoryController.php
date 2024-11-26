<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
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

class CategoryController extends V5Controller
{
    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function show(Request $request, int $id): CategoryResource
    {

        $category = $this->queryBus->handle(
            FetchCategoryByIdQuery::fromRequest($id, $request)
        );
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
                FetchAllCategoriesQuery::fromRequest($request)
            )
        );
    }

    private function getCategory(int $id, ?array $fields = null, ?array $haydrates = null)
    {
        if (!$fields) {
            $fields = Category::ALLOWED_FIELDS;
        }
        if (!$haydrates) {
            $haydrates = array_keys(Category::ALLOWED_RELATIONSHIPS);
        }
        $query = new FetchCategoryByIdQuery($id);
        $query->addOnlyValues(
            $fields,
            $haydrates,
            Category::ALLOWED_RELATIONSHIPS,
            Category::REQUIRED_FIELDS
        );
        return $this->queryBus->handle($query);
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

        $id = $this->commandBus->handle(StoreCategoryCommand::createFromRequest($request));

        $category = $this->getCategory($id, Category::ALLOWED_FIELDS);

        return new CategoryResource($category);
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
        $category = $this->getCategory($id, Category::ALLOWED_FIELDS, []);

        $this->authorize('update', $category);
        $this->commandBus->handle(UpdateCategoryCommand::fromRequest($id, $request, $category));
        return new CategoryResource($this->getCategory($id, Category::ALLOWED_FIELDS));
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

        $category = $this->getCategory($id, ['id'], []);
        $this->authorize('delete', $category);

        $this->commandBus->handle(new DeleteCategoryCommand($id));
        return $this->deleteResponse($id);
    }
}
