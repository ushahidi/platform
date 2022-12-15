<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use App\Bus\Command\CommandBus;
use App\Bus\Query\QueryBus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Ushahidi\Modules\V5\Actions\Category\Commands\DeleteCategoryCommand;
use Ushahidi\Modules\V5\Actions\Category\Commands\StoreCategoryCommand;
use Ushahidi\Modules\V5\Actions\Category\Commands\UpdateCategoryCommand;
use Ushahidi\Modules\V5\Http\Resources\CategoryCollection;
use Ushahidi\Modules\V5\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Models\Category;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Actions\Category\Queries\FetchAllCategoriesQuery;
use Ushahidi\Modules\V5\Actions\Category\Queries\FetchCategoryByIdQuery;
use Ushahidi\Modules\V5\Requests\StoreCategoryRequest;
use Ushahidi\Modules\V5\Requests\UpdateCategoryRequest;

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
     * @throws AuthorizationException
     */
    public function show(int $id): CategoryResource
    {
        $category = $this->queryBus->handle(new FetchCategoryByIdQuery($id));

        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     *
     * @return CategoryCollection
     * @throws AuthorizationException
     */
    public function index()
    {
        return new CategoryCollection(
            $this->queryBus->handle(
                new FetchAllCategoriesQuery()
            )
        );
    }
     /**
     * Display the specified resource.
     *
     * @param StoreCategoryRequest $request
     * @return ResponseFactory|Application|JsonResponse|Response
     * @throws AuthorizationException
     */
    public function store(StoreCategoryRequest $request)
    {
        DB::beginTransaction();
        try {
            $id = $this->commandBus->handle(StoreCategoryCommand::createFromRequest($request));

            $category = $this->queryBus->handle(new FetchCategoryByIdQuery($id));
            $errors = $this->saveTranslations(
                $category,
                $category->toArray(),
                $request->input('translations') ?? [],
                $category->id,
                'category'
            );

            if (!empty($errors)) {
                DB::rollback();
                return response()->json($errors, 403);
            }

            DB::commit();
            $resource = new CategoryResource($category);
            return response(['result' => $resource], 201);
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
    public function update(int $id, UpdateCategoryRequest $request)
    {
        DB::beginTransaction();
        try {
            $command = UpdateCategoryCommand::fromRequest($id, $request);
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
                return self::make422($errors, 'translation');
            }
            DB::commit();
            return new CategoryResource($category);
        } catch (\Exception $e) {
            DB::rollback();
            if ($e instanceof ModelNotFoundException) {
                return self::make404();
            }
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
        /// $this->authorize('delete', $category); todo: should be done before controller double check if isn't
        try {
            $this->commandBus->handle(new DeleteCategoryCommand($id));
            return response()->json(['result' => ['deleted' => $id]]);
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return self::make404();
            }
            return self::make500($e->getMessage());
        }
    }
}
