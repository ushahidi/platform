<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use App\Bus\Query\QueryBus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\Post\Queries\FindPostByIdQuery;
use Ushahidi\Modules\V5\Actions\Post\Queries\ListPostsQuery;
use Ushahidi\Modules\V5\Actions\Post\Commands\DeletePostCommand;
use Ushahidi\Modules\V5\Actions\Post\Commands\CreatePostCommand;
use Ushahidi\Modules\V5\Actions\Post\Commands\UpdatePostCommand;
use Ushahidi\Modules\V5\Events\PostCreatedEvent;
use Ushahidi\Modules\V5\Events\PostUpdatedEvent;
use Ushahidi\Modules\V5\Http\Resources\PostResource as OldPostResource;
use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Modules\V5\Models\Post\PostStatus;
use Ushahidi\Modules\V5\Exceptions\V5Exception;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Common\ValidatorRunner;

use Ushahidi\Modules\V5\Http\Resources\Post\PostCollection;
use Ushahidi\Modules\V5\Http\Resources\Post\PostResource;
use Ushahidi\Modules\V5\Http\Resources\Post\PostLockResource;
use Ushahidi\Modules\V5\Requests\PostRequest;

use Ushahidi\Modules\V5\Actions\Post\Commands\UpdatePostLockCommand;
use Ushahidi\Modules\V5\Actions\Post\Commands\DeletePostLockCommand;
use Ushahidi\Modules\V5\Actions\Post\Queries\FetchPostLockByPostIdQuery;
use Ushahidi\Modules\V5\Actions\Post\Queries\FindPostGeometryByIdQuery;
use Ushahidi\Modules\V5\Actions\Post\Queries\ListPostsGeometryQuery;
use Ushahidi\Modules\V5\Actions\Post\Queries\PostsStatsQuery;
use Ushahidi\Modules\V5\Http\Resources\Post\PostGeometryCollection;
use Ushahidi\Modules\V5\Http\Resources\Post\PostGeometryResource;
use Ushahidi\Modules\V5\Http\Resources\Post\PostStatsResource;
use Ushahidi\Core\Tool\Tile;
use Ushahidi\Modules\V5\Actions\Survey\Queries\GetSurveyIdsWithPrivateLocationQuery;

use Ushahidi\Contracts\Permission;
use Ushahidi\Core\Concerns\AdminAccess;

use Ushahidi\Modules\V5\Actions\Contact\Commands\CreateContactCommand;
use Ushahidi\Modules\V5\Actions\Contact\Queries\FetchContactQuery;
use Ushahidi\Modules\V5\Actions\Contact\Queries\FetchContactByIdQuery;
use Ushahidi\Modules\V5\Models\Contact;
use Ushahidi\Contracts\Sources;
use Ushahidi\Contracts\Contact as ContractContact;

class PostController extends V5Controller
{

    // It uses methods from several traits to check access:
    // - `AdminAccess` to check if the user has admin access
    use AdminAccess;


    /**
     * Not all fields are things we want to allow on the body of requests
     * an author won't change after the fact, so we limit that change
     * to avoid issues from the frontend.
     * @return string[]
     */
    protected function ignoreInput()
    {
        return ['author_email', 'slug', 'user_id', 'author_realname', 'created', 'updated'];
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse|PostResource
     */
    public function show(int $id, Request $request)
    {
        $post = $this->queryBus->handle(FindPostByIdQuery::FromRequest($id, $request));
        $this->authorizeAnyone('show', $post);

        return new PostResource($post);
    }

    public function index(Request $request): PostCollection
    {
        $this->authorizeAnyone('index', Post::class);

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
     * Gets the whatsapp contact from the request. If there are no contacts it creates one
     *
     * @param Request $request
     * @return Contact
     */
    private function getWhatsappContact(PostRequest $request):Contact
    {
        $search_request = new Request();
        $search_request->merge([
            'data_source'=>Sources::WHATSAPP,
            'contact'=>$request->input('contact')['contact']
        ]);
        $contacts = $this->queryBus->handle(FetchContactQuery::FromRequest($search_request));

        if (count($contacts) > 0) {
            $contact =  $contacts->first();
        } else {
            $contact_id =  $this->commandBus->handle(
                CreateContactCommand::forWhatsapp(
                    $request->input('user_id'),
                    $request->input('contact')['contact'],
                    $request->input('contact')['type'] ?? ContractContact::PHONE,
                    $request->input('contact')['can_notify'] ?? 0
                )
            );
            $contact = $this->queryBus->handle(new FetchContactByIdQuery($contact_id));
        }
        
        return $contact;
    }

    private function getPost(int $id, ?array $fields = null, ?array $haydrates = null)
    {
        if (!$fields) {
            $fields = Post::ALLOWED_FIELDS;
        }
        if (!$haydrates) {
            $haydrates = array_keys(Post::ALLOWED_RELATIONSHIPS);
        }
        $find_post_query = new FindPostByIdQuery($id);
        $find_post_query->addOnlyValues(
            $fields,
            $haydrates,
            Post::ALLOWED_RELATIONSHIPS,
            Post::REQUIRED_FIELDS
        );
        return $this->queryBus->handle($find_post_query);
    }
    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return PostResource|JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(PostRequest $request)
    {

        $this->runAuthorizer('store', [Post::class, $request->input('form_id'), $this->getUser()->getId()]);
        if ($request->input('source') === 'whatsapp') {
            $contact = $this->getWhatsappContact($request);
            $request->merge([
                'contact_id'=>$contact->id,
                // To Do: this temporay soluation to save the contact object in posts metadata,
                // bu this we can avoid extra diplicated join relation!
                'contact' => $contact
                 ]) ;
        }
        $id = $this->commandBus->handle(CreatePostCommand::createFromRequest($request));
        $post = $this->getPost($id);
        event(new PostCreatedEvent($post));
        return new PostResource($post, 201);
    } //end store()

    /**
     * Patch the status of a post
     * @TODO: add all patch features. Right now we cover status only
     * @param int $id
     * @param Request $request
     * @return PostResource|JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function patch(int $id, Request $request)
    {
        $post = Post::find($id);
        $status = $this->getField('status', $request->input('status'));
        if (!$post) {
            return self::make404();
        }
        if (!$status) {
            return self::make422("The V5 API requires a status for post status updates.");
        }

        DB::beginTransaction();
        try {
            // TODO: $post->doStatusTransition($status);
            $post->setAttribute('status', $status);
            $this->authorize('changeStatus', $post);

            if ($post->save()) {
                DB::commit();
                // note: done after commit to avoid deadlock in the db
                // see comment in bulkPatchOperation() below
                event(new PostUpdatedEvent($post));
                return new OldPostResource($post);
            } else {
                DB::rollback();
                return self::make422($post->errors);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return self::make500($e->getMessage());
        }
    } // end patchStatus

    /**
     * @param Request $request
     * @NOTE: only supports status updates
     * @return JsonResponse|PostResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * // one of the ids is a 404 -- (done)
     * // duplicated ids in the list - (done)
     * // statuses that are invalid (done)
     * // empty list (done)
     * // empty items within list (done)
     * // missing id or status fields within items (done)
     * // randomness ? things going wrong and we don't know why
     */
    public function bulkOperation(Request $request)
    {
        $input = $request->input();
        // sanity check bulk envelope
        $v = $this->bulkValidateEnvelope($input);
        if (!$v->success()) {
            return self::make422($v->errors);
        }

        $operation = $request->input('operation');
        $items = $request->input('items');
        switch ($operation) {
            case 'patch':
                return $this->bulkPatchOperation($items);
            case 'delete':
                return $this->bulkDeleteOperation($items);
        }
    }

    private function bulkPatchOperation($items)
    {
        $p = new Post();
        $validation = ValidatorRunner::runValidation(
            ['items' => $items],
            $p->getBulkPatchRules(),
            $p->bulkPatchValidationMessages()
        );
        if (!$validation->success()) {
            return self::make422($p->errors);
        }

        //
        $bulk_ids = $this->bulkGetIds($items);
        $posts = Post::whereIn('id', $bulk_ids)->get();
        DB::beginTransaction();
        try {
            $data = $this->bulkGetFields($items, ['id', 'status']);
            foreach ($posts as $post) {
                $this->authorize('update', $post);
                $status = PostStatus::normalize(Arr::get($data->firstWhere('id', $post->id), 'status'));
                $post->setAttribute('status', $status);
                // TODO: $post->doStatusTransition($status);
                $saved = $post->save();

                if (!$saved) {
                    throw new V5Exception("Could not save post status update - unknown error");
                }
            }
            DB::commit();
        } catch (V5Exception $e) {
            DB::rollback();
            return self::make500($e->getMessage());
        } catch (AuthorizationException $e) {
            DB::rollback();
            return self::make403($e->getMessage());
        } catch (\Exception $e) {
            DB::rollback();
            return self::make500();
        }
        // Note: DB transactions
        // This is done outside the try{} block so that it happens
        // after the DB::commit above. The reason is that this will
        // trigger v3/Kohana code, which will try to open its own
        // database connection. Both transactions cannot be open at
        // the same time, as they hold exclusive locks.
        foreach ($posts as $post) {
            event(new PostUpdatedEvent($post));
        }
        return response()->json(['status' => 'completed'], 200);
    }

    private function bulkDeleteOperation($items)
    {
        $p = new Post();
        $v = ValidatorRunner::runValidation(
            ['items' => $items],
            $p->getBulkDeleteRules(),
            $p->bulkDeleteValidationMessages()
        );
        if ($v->fails()) {
            return self::make422($p->errors);
        }

        $bulk_ids = $this->bulkGetIds($items);
        $posts = Post::whereIn('id', $bulk_ids)->get();
        DB::beginTransaction();
        try {
            foreach ($posts as $post) {
                $this->authorize('delete', $post);
                // translations delete can find 0 records, so we don't throw here
                // if something goes wrong we'll get a query exception which will result in a generic issue
                $post->translations()->delete();
                if ($post->delete() < 1) {
                    throw new V5Exception(
                        trans('errors.delete_failed', [
                            'model' => 'post',
                            'id' => $post->id
                        ])
                    );
                }
            }
            DB::commit();
        } catch (V5Exception $e) {
            DB::rollback();
            return self::make500($e->getMessage());
        } catch (AuthorizationException $e) {
            DB::rollback();
            return self::make403($e->getMessage());
        } catch (\Exception $e) {
            DB::rollback();
            return self::make500();
        }

        return response()->json(['status' => 'completed'], 200);
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
    public function update(int $id, PostRequest $request)
    {
        $old_post = $this->getPost($id);
        $this->authorize('update', $old_post);
        $this->commandBus->handle(UpdatePostCommand::fromRequest($id, $request, $old_post));
        $post = $this->getPost($id);
        event(new PostUpdatedEvent($post));
        return new PostResource($post);
    } //end update()

    /**
     * @param Post $post
     * @param array $entity_array
     * @param array $translations
     * @return array
     */
    // public function validateTranslations($post, $entity_array, array $translations)
    // {
    //     $entity_array = array_merge($entity_array, $translations);
    //     if (isset($entity_array['slug'])) {
    //         $entity_array['slug'] = Post::makeSlug($entity_array['slug']);
    //     }
    //     if (!$post->validate($entity_array)) {
    //         return $post->errors->toArray();
    //     }
    //     return [];
    // }

    /**
     * @param integer $id
     */
    public function delete(int $id, Request $request)
    {
        $post = $this->getPost($id, ['id', 'status','user_id'], []);
        $this->authorize('delete', $post);
        $this->commandBus->handle(new DeletePostCommand($id));
        return $this->deleteResponse($id);
    } //end delete()


    public function stats(Request $request)
    {
        if ($this->canUserseePostsWithPrivateLocation()) {
            $stats = $this->queryBus->handle(PostsStatsQuery::FromRequest($request));
        } else {
            $stats = $this->queryBus->handle(
                PostsStatsQuery::FromRequest(
                    $request,
                    $this->queryBus->handle(new GetSurveyIdsWithPrivateLocationQuery())->pluck('id')->toArray()
                )
            );
        }

        return new PostStatsResource($stats);
    }

    public function indexGeoJson(Request $request): PostGeometryCollection
    {
        if ($this->canUserseePostsWithPrivateLocation()) {
            $posts = $this->queryBus->handle(ListPostsGeometryQuery::FromRequest($request));
        } else {
            $posts = $this->queryBus->handle(
                ListPostsGeometryQuery::FromRequest(
                    $request,
                    $this->queryBus->handle(new GetSurveyIdsWithPrivateLocationQuery())->pluck('id')->toArray()
                )
            );
        }

        return new PostGeometryCollection($posts);
    }

    public function indexGeoJsonWithZoom(Request $request): PostGeometryCollection
    {
        $this->prepBoundingBox($request);
        return $this->indexGeoJson($request);
    }

    private function canUserseePostsWithPrivateLocation()
    {
        $authorizer = service('authorizer.post');
        $user = $authorizer->getUser();
        if ($this->isUserAdmin($user)) {
            return true;
        }
        if ($authorizer->acl->hasPermission($user, Permission::MANAGE_POSTS)) {
            return true;
        }
        return false;
    }

    public function prepBoundingBox(Request $request)
    {
        $params = $request->route()->parameters();

        // If zoom/x/y are passed get bounding box
        $zoom = isset($params['zoom']) ? $params['zoom'] : false;
        $x = isset($params['x']) ? $params['x'] : false;
        $y = isset($params['y']) ? $params['y'] : false;
        if ($zoom !== false and
            $x !== false and
            $y !== false
        ) {
            $boundingBox = Tile::pointToBoundingBox($zoom, $x, $y);
            $request->merge(['bbox' => implode(',', $boundingBox->asArray())]);
        }
    }


    public function showPostGeoJson($id, Request $request): PostGeometryResource
    {
        $post_geometry = $this->queryBus->handle(FindPostGeometryByIdQuery::FromRequest($id, $request));
        return new PostGeometryResource($post_geometry);
    }


    public function updateLock(int $post_id, Request $request)
    {
        $post = $this->getPost($post_id, ['id', 'status','user_id','form_id'], []);
        $this->authorize('update', $post);

        $this->commandBus->handle(new UpdatePostLockCommand($post_id));
        $post_lock = $this->queryBus->handle(new FetchPostLockByPostIdQuery($post_id));
        return new PostLockResource($post_lock);
    }


    public function deleteLock(int $post_id, Request $request)
    {
        $post = $this->getPost($post_id, ['id', 'status','user_id','form_id'], []);
        $this->authorize('update', $post);

        $this->commandBus->handle(new DeletePostLockCommand($post_id));
        return $this->deleteResponse($post_id);
    }
} //end class
