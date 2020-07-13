<?php

namespace v5\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Ushahidi\App\Auth\GenericUser;
use Illuminate\Http\Request;
use v5\Http\Resources\PostCollection;
use v5\Http\Resources\PostResource;
use v5\Models\Post;
use v5\Models\Translation;
use Illuminate\Support\Facades\DB;

class PostController extends V4Controller
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
        $post = Post::find($id);
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
        return new PostCollection(Post::paginate(20));
    }//end index()


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
        // Check post permissions
        $user = $this->runAuthorizer('store', [Post::class, $input['form_id']]);
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
            $this->savePostValues($post, $input['post_content'], $post->id);
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
            return new PostResource($post);
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
            $this->savePostValues($post, $post_values, $post->id);
            $this->updateTranslations(new Post(), $post->toArray(), $request->input('translations'), $post->id, 'post');
            DB::commit();
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
        $post->valuesPostTag()->delete();
        foreach ($post_content as $stage) {
            if (!isset($stage['fields'])) {
                continue;
            }
            foreach ($stage['fields'] as $field) {
                if (!isset($field['value']) || !isset($field['value']['value'])) {
                    continue;
                }
                $update_id = isset($field['value']['id']) ? $field['value']['id'] : null;
                $value = $field['value']['value'];
                $value_translations = isset($field['value']['translations']) ? $field['value']['translations'] : [];
                $type = $field['type'];

                if ($type === 'tags') {
                    $type === 'tags' ? 'tag' : $type;
                    $this->savePostTags($post, $field['id'], $value);
                    continue;
                }

                $class_name = "v5\Models\PostValues\Post" . ucfirst($type);
                if (!class_exists($class_name)) {
                    throw new \Exception("Type '$type' is invalid.");
                }
                $post_value = new $class_name();
                if ($update_id) {
                    $post_value = $class_name::select('post_'.$type.'.*')
                                    ->where('post_'.$type.'.id', $update_id)
                                    ->get()
                                    ->first();
                }

                if ($type === 'point') {
                    $value = \DB::raw("GeomFromText('POINT({$value['lon']} {$value['lat']})')");
                }

                if ($type === 'geometry') {
                    $value = \DB::raw("GeomFromText('$value')");
                }

                $data = [
                    'post_id' => $post_id,
                    'form_attribute_id' => $field['id'],
                    'value' => $value
                ];
                $validation = $post_value->validate([
                    'post_id' => $post_id,
                    'form_attribute_id' => $field['id'],
                    'value' => $value
                ]);
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
                }
            }
        }
    }

    protected function savePostTags($post, $attr_id, $tags)
    {
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
