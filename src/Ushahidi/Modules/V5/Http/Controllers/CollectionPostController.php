<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\Post\Queries\FindPostByIdQuery;
use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Modules\V5\Http\Resources\Collection\CollectionPostResource;
use Ushahidi\Modules\V5\Http\Resources\Post\PostCollection;
use Ushahidi\Modules\V5\Http\Resources\Post\PostResource;

use Ushahidi\Modules\V5\Actions\Post\Queries\ListPostsQuery;
use Ushahidi\Modules\V5\Actions\Collection\Queries\FetchCollectionByIdQuery;
use Ushahidi\Modules\V5\Actions\Collection\Queries\FetchCollectionPostByIdQuery;
use Ushahidi\Modules\V5\Actions\Collection\Commands\DeleteCollectionPostCommand;
use Ushahidi\Modules\V5\Actions\Collection\Commands\CreateCollectionPostCommand;
use Vonage\SMS\Collection;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Requests\CollectionPostRequest;

class CollectionPostController extends V5Controller
{

    /**
     * @throws ExceptionNotFound
     */
    private function checkCollectionIsFound(int $collection_id)
    {
        $this->queryBus->handle(new FetchCollectionByIdQuery($collection_id));
    }

    /**
     * @throws ExceptionNotFound
     */
    private function checkPostIsFoundInCollection(int $collection_id, int $post_id)
    {
        $this->queryBus->handle(new FetchCollectionPostByIdQuery($collection_id, $post_id));
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse|PostResource
     */
    public function show(int $collection_id, int $id, Request $request): PostResource
    {
        $this->checkCollectionIsFound($collection_id);
        

        $this->checkPostIsFoundInCollection($collection_id, $id);

        $post = $this->queryBus->handle(FindPostByIdQuery::FromRequest($id, $request));
        return new PostResource($post);
    }

    public function index(int $collection_id, Request $request): PostCollection
    {
        $this->checkCollectionIsFound($collection_id);

        $request->merge(['set' => $collection_id]);

        $posts = $this->queryBus->handle(ListPostsQuery::FromRequest($request));
        return new PostCollection($posts);
    }



    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return CollectionPostResource|JsonResponse
     * @throws ExceptionNotFound
     */
    public function store(int $collection_id, CollectionPostRequest $request): CollectionPostResource
    {

        $collection =  $this->queryBus->handle(new FetchCollectionByIdQuery($collection_id));
      // $this->authorize('edit', $collection);

        $post_id = $request->input('post_id');

        $this->commandBus->handle(new CreateCollectionPostCommand($collection_id, $post_id));

        return new CollectionPostResource(collect(['collection_id' => $collection_id, 'post_id' => $post_id]), 201);
    } //end store()

    /**
     * @param integer $id
     */
    public function delete(int $collection_id, int $id, Request $request)
    {
        $this->checkCollectionIsFound($collection_id);

       // $post = $this->queryBus->handle(new FindPostByIdQuery($id, ['id', 'user_id']));
       // $this->authorize('delete', $post);
        
        $this->checkPostIsFoundInCollection($collection_id, $id);


        $this->commandBus->handle(new DeleteCollectionPostCommand($collection_id, $id));

        return $this->deleteResponse($id);
    } //end delete()
} //end class
