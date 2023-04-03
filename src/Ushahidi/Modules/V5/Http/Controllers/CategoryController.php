<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
use Ushahidi\Modules\V5\Requests\StoreCategoryRequest;
use Ushahidi\Modules\V5\Requests\UpdateCategoryRequest;


use Illuminate\Support\Facades\Validator;
use Ushahidi\Modules\V5\Models\Category;
use Ushahidi\Modules\V5\Http\Requests\CategoryRequest;
use Ushahidi\Modules\V5\DTO\CategorySearchFields;
use Ushahidi\Modules\V5\Models\User;

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
//try{
        $category = $this->queryBus->handle(new FetchCategoryByIdQuery($id));
//     }catch(\Exception $e){
// //dd(get_class($e));
// throw $e;
    
// }

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
        return new CategoryCollection(
            $this->queryBus->handle(
                new FetchAllCategoriesQuery(new Paging($request), new CategorySearchFields($request))
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
    // public function update(CategoryRequest $request)
    // {
    //     // Doing this so tests can pass, apparently the test suite
    //     // assumes finding the resource before validation.
    //     // This is achievable if we use route model binding.
    //     $category = $request->category;

    //     $this->authorize('update', $category);

    //     DB::beginTransaction();
    //     try {
    //         $category->update($request->validated());
    //         $errors = $this->updateTranslations(
    //             $category,
    //             $category->toArray(),
    //             $request->input('translations') ?? [],
    //             $category->id,
    //             'category'
    //         );

    //         if (!empty($errors)) {
    //             DB::rollback();
    //             return response()->json($errors, 403);
    //         }

    //         DB::commit();
    //         $resource = new CategoryResource($category);
    //         return response(['result' => $resource], 201);
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return self::make500($e->getMessage());
    //     }
    // }

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
            $entity_array['slug'] = $category::makeSlug($entity_array['slug']);
        }

        $request = new CategoryRequest;

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
       // dd($category);
     //   $this->authorize('delete', $category);
        $this->commandBus->handle(new DeleteCategoryCommand($id));
        return $this->deleteResponse($id);

        // $category = Category::withoutGlobalScopes()->find($id);
        // if (!$category) {
        //     return self::make404();
        // }

        // $this->authorize('delete', $category);

        // $success = DB::transaction(function () use ($category) {
        //     $category->translations()->delete();
        //     $success = $category->delete();
        //     return $success;
        // });

        // if ($success) {
        //    return response()->json(['result' => ['deleted' => $id]]);
        // } catch (\Exception $e) {
        //     if ($e instanceof ModelNotFoundException) {
        //         return self::make404();
        //     }
        //     return self::make500($e->getMessage());
        // }
    }

/**
 * Not all fields are things we want to allow on the body of requests
 * an author won't change after the fact so we limit that change
 * to avoid issues from the frontend.
 * @return string[]
 */
// protected function ignoreInput()
// {
//     return ['author_email', 'slug', 'user_id', 'author_realname', 'created', 'updated'];
// }
}
