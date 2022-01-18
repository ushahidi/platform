<?php

namespace v5\Http\Controllers;

use http\Env\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Exception;
use Ushahidi\App\Auth\GenericUser;
use Illuminate\Http\Request;
use v5\Events\PostCreatedEvent;
use v5\Events\PostUpdatedEvent;
use v5\Http\Resources\PostCollection;
use v5\Http\Resources\PostResource;
use v5\Models\Post\Post;
use v5\Models\Post\PostStatus;
use v5\Models\Translation;
use v5\Exceptions\V5Exception;
use Illuminate\Support\Facades\DB;
use v5\Common\ValidatorRunner;

class PostController extends V5Controller
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

    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $post = Post::withPostValues()->where('id', $id)->first();

        if (!$post) {
            return self::make404();
        }

        return new PostResource($post);
    }//end show()


    /**
     * Display the specified resource.
     *
     * @return PostCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        return new PostCollection(Post::withPostValues()->paginate(20));
    }//end index()

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

    private function setInputDefaults($input, $user, $action)
    {
        if ($action === 'store') {
            $input['slug'] = Post::makeSlug($input['slug'] ?? $input['title']);
            $input['user_id'] = $input['user_id'] ?? $user->getId();
            $input['author_email'] = $input['author_email'] ?? $user->email;
            $input['author_realname'] = $input['author_realname'] ?? $user->realname;
        }
        return $input;
    }
    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return PostResource|JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $input = $this->getFields($request->input());
        if (empty($input)) {
            return self::make500('POST body cannot be empty');
        }
        if (empty($input['form_id'])) {
            return self::make422("The V5 API requires a form_id for post creation.");
        }
        // Check post permissions
        $user = $this->runAuthorizer('store', [Post::class, $input['form_id'], $this->getUser()->getId()]);
        $input = $this->setInputDefaults($input, $user, 'store');
        $post = new Post();
        if (!$post->validate($input)) {
            return self::make422($post->errors);
        }
        DB::beginTransaction();
        try {
            $post = Post::create(
                array_merge(
                    $input,
                    ['created' => time()]
                )
            );
            if (isset($input['completed_stages'])) {
                $this->savePostStages($post, $input['completed_stages']);
            }

            // Attempt auto-publishing post on creation
            if ($post->tryAutoPublish()) {
                $post->save();
            }

            $errors = $this->savePostValues($post, $input['post_content'], $post->id);

            if (!empty($errors)) {
                DB::rollback();
                return self::make422($errors, 'fields');
            }
            $errors = $this->saveTranslations(
                $post,
                $post->toArray(),
                $request->input('translations') ?? [],
                $post->id,
                'post'
            );
            if (!empty($errors)) {
                DB::rollback();
                return self::make422($errors, 'translation');
            }
            DB::commit();
            // note: done after commit to avoid deadlock in the db
            // see comment in bulkPatchOperation() below
            event(new PostCreatedEvent($post));
            return new PostResource($post);
        } catch (\Exception $e) {
            DB::rollback();
            return self::make500($e->getMessage());
        }
    }//end store()

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
                return new PostResource($post);
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
        return response()->json([ 'status' => 'completed' ], 200);
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

        return response()->json([ 'status' => 'completed' ], 200);
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
    public function update(int $id, Request $request)
    {
        $post = Post::find($id);
        if (!$post) {
            return self::make404();
        }
        $this->authorize('update', $post);
        $input = $this->getFields($request->input());
        $post_values = $input['post_content'];
        if (!$post->slug) {
            $input['slug'] = Post::makeSlug($input['slug'] ?? $input['title']);
        }
        if (!$post->validate($input)) {
            return self::make422($post->errors);
        }
        DB::beginTransaction();
        try {
            $post->update(array_merge($input, ['updated' => time()]));

            if (isset($input['completed_stages'])) {
                $this->savePostStages($post, $input['completed_stages']);
            }

            // TODO: handle status update?

            $errors = $this->savePostValues($post, $post_values, $post->id);
            if (!empty($errors)) {
                DB::rollback();
                return self::make422($errors);
            }
            $translations_input = $request->input('translations') ? $request->input('translations') : [];
            $this->updateTranslations(new Post(), $post->toArray(), $translations_input, $post->id, 'post');
            DB::commit();

            // note: done after commit to avoid deadlock in the db
            // see comment in bulkPatchOperation()
            event(new PostUpdatedEvent($post));
            $post->load('translations');
            return new PostResource($post);
        } catch (\Exception $e) {
            DB::rollback();
            return self::make500($e->getMessage());
        }
    }//end update()

    protected function savePostStages($post, $completed)
    {
        $post->postStages()->delete();
        foreach ($completed as $stage_id) {
            $post->postStages()->create(['post_id' => $post, 'form_stage_id' => $stage_id, 'completed' => 1]);
        }
    }

    /**
     * @param Post $post
     * @param array $post_content
     * @param int $post_id
     * @throws \Exception
     * Stage: fields
     * Fields: value, type, id
     */
    protected function savePostValues(Post $post, array $post_content, int $post_id)
    {
        $errors = [];
        $post->valuesPostTag()->delete();
        foreach ($post_content as $stage) {
            if (!isset($stage['fields'])) {
                continue;
            }
            foreach ($stage['fields'] as $field) {
                if (!isset($field['value']) || !isset($field['value']['value'])) {
                    continue;
                }
                $value = $field['value']['value'];
                $value_translations = isset($field['value']['translations']) ? $field['value']['translations'] : [];
                $type = $field['type'];

                if ($type === 'tags') {
                    $type === 'tags' ? 'tag' : $type;
                    $this->savePostTags($post, $field['id'], $value);
                    continue;
                }

                $class_name = "v5\Models\PostValues\Post" . ucfirst($type);
                if (!class_exists($class_name) &&
                    in_array(
                        $class_name,
                        [
                            'v5\Models\PostValues\PostTitle',
                            'v5\Models\PostValues\PostDescription'
                        ]
                    )
                ) {
                    continue;
                } elseif (!class_exists($class_name)) {
                    throw new \Exception("Type '$type' is invalid.");
                }

                $post_value = $class_name::select('post_'.$type.'.*')
                                    ->where('post_'.$type.'.form_attribute_id', $field['id'])
                                    ->where('post_'.$type.'.post_id', $post_id)
                                    ->get()
                                    ->first();
                $update_id = $post_value ? $post_value->id : null;
                if (!$update_id) {
                    $post_value = new $class_name;
                }
                if ($type === 'geometry') {
                    if (is_string($value) && $value === '') {
                        $value = null;
                    } else {
                        $value = \DB::raw("ST_GeomFromText('$value')");
                    }
                }

                $data = [
                    'post_id' => $post_id,
                    'form_attribute_id' => $field['id'],
                    'value' => $value
                ];
                foreach ($data as $k => $v) {
                    $post_value->setAttribute($k, $v);
                }

                $validation = $post_value->validate();

                if ($type === 'point') {
                    $data['value'] = \DB::raw("ST_GeomFromText('POINT({$value['lon']} {$value['lat']})')");
                }
                if ($validation) {
                    if ($update_id) {
                        $post_value->update($data);
                        $this->updateTranslations(
                            new $class_name(),
                            $post_value->toArray(),
                            $value_translations,
                            $update_id,
                            "post_value_$type"
                        );
                    } else {
                        $field_value = get_class($post_value)::create($data);
                        $this->saveTranslations(
                            new $class_name(),
                            $field_value->toArray(),
                            $value_translations,
                            $field_value->id,
                            "post_value_$type"
                        );
                    }
                } else {
                    $errors['task_id.' . $stage['id'] . '.field_id.' . $field['id']] = $post_value->errors->toArray();
                }
            }
        }
        return $errors;
    }

    protected function savePostTags($post, $attr_id, $tags)
    {
        if (!is_array($tags)) {
            throw new \Exception("$attr_id: tag format is invalid.");
        }
        foreach ($tags as $tag_id) {
            $post->valuesPostTag()->create(
                [
                    'post_id' => $post->id,
                    'form_attribute_id' => $attr_id,
                    'tag_id' => $tag_id
                ]
            );
        }
    }

    /**
     * @param Post $post
     * @param array $entity_array
     * @param array $translations
     * @return array
     */
    public function validateTranslations($post, $entity_array, array $translations)
    {
        $entity_array = array_merge($entity_array, $translations);
        if (isset($entity_array['slug'])) {
            $entity_array['slug'] = Post::makeSlug($entity_array['slug']);
        }
        if (!$post->validate($entity_array)) {
            return $post->errors->toArray();
        }
        return [];
    }

    /**
     * @param integer $id
     */
    public function delete(int $id, Request $request)
    {
        $post = Post::find($id);
        $this->authorize('delete', $post);
        $success = DB::transaction(function () use ($id, $request, $post) {
            $post->translations()->delete();
            return $post->delete();
        });
        if ($success) {
            return response()->json(['result' => ['deleted' => $id]]);
        } else {
            return self::make500('Could not delete model');
        }
    }//end delete()
}//end class
