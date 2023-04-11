<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\Post\Queries\FindPostByIdQuery;
use Ushahidi\Modules\V5\Actions\Post\Queries\ListPostsQuery;
use Ushahidi\Modules\V5\Actions\Post\Commands\DeletePostCommand;
use Ushahidi\Modules\V5\Actions\Post\Commands\CreatePostCommand;
use Ushahidi\Modules\V5\Events\PostCreatedEvent;
use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Modules\V5\Http\Resources\Post\PostCollection ;
use Ushahidi\Modules\V5\Http\Resources\Post\PostResource ;
use Ushahidi\Modules\V5\Requests\PostRequest;

use Ushahidi\Modules\V5\Actions\Collection\Queries\FetchCollectionByIdQuery;
use Ushahidi\Modules\V5\Actions\Collection\Queries\FetchCollectionPostByIdQuery;
use Ushahidi\Modules\V5\Actions\Collection\Commands\DeleteCollectionPostCommand;
use Ushahidi\Modules\V5\Actions\Collection\Commands\CreateCollectionPostCommand;

class CollectionPostController extends V5Controller
{

    /**
     * @throws ExceptionNotFound
     */
    private function checkCollectionIsFound(int $collection_id)
    {
         $this->queryBus->handle(new FetchCollectionByIdQuery($collection_id));
    }

    private function checkPostIsFoundInCollection(int $collection_id, int $post_id)
    {
        $this->queryBus->handle(new FetchCollectionPostByIdQuery($collection_id, $post_id));
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse|PostResource
     */
    public function show(int $collection_id, int $id, Request $request):PostResource
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

    private function getUser()
    {
        $authorizer = service('authorizer.post');
        return $authorizer->getUser();
    }

    private function runAuthorizer($ability, $object)
    {
        $authorizer = service('authorizer.post');
        // if there's no user the guards will kick them off already, but if there
        // is one we need to check the authorizer to ensure we don't let
        // users without admin perms create forms etc
        // this is an unfortunate problem with using an old version of lumen
        // that doesn't let me do guest user checks without adding more risk.
        $user = $authorizer->getUser();
        $this->authorizeAnyone($ability, $object);
        return $user;
    }

 
    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return PostResource|JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(int $collection_id, PostRequest $request):PostResource
    {

        $this->runAuthorizer('store', [Post::class, $request->input('form_id'), $this->getUser()->getId()]);
        $this->checkCollectionIsFound($collection_id);

        //To do : transaction
        $id = $this->commandBus->handle(CreatePostCommand::createFromRequest($request));
        $this->commandBus->handle(new CreateCollectionPostCommand($collection_id, $id));

        $post = $this->queryBus->handle(
            new FindPostByIdQuery(
                $id,
                Post::ALLOWED_FIELDS,
                array_keys(Post::ALLOWED_RELATIONSHIPS)
            )
        );
        event(new PostCreatedEvent($post));
        return new PostResource($post, 201);
    } //end store()

    /**
     * @param integer $id
     */
    public function delete(int $collection_id, int $id, Request $request)
    {

        $this->checkCollectionIsFound($collection_id);

        $post = $this->queryBus->handle(new FindPostByIdQuery($id, ['id', 'user_id']));
        $this->authorize('delete', $post);
        
        $this->checkPostIsFoundInCollection($collection_id, $id);


        //To do : Transaction
        $this->commandBus->handle(new DeletePostCommand($id));
        $this->commandBus->handle(new DeleteCollectionPostCommand($collection_id, $id));

        return $this->deleteResponse($id);
    } //end delete()
} //end class
