<?php

namespace v4\Http\Controllers;

use Illuminate\Auth\Access\Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;
use Ushahidi\App\Validator\LegacyValidator;
use v4\Http\Resources\CategoryCollection;
use v4\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use v4\Http\Resources\PostCollection;
use v4\Http\Resources\PostResource;
use v4\Models\Attribute;
use v4\Models\Category;
use v4\Models\Post;
use v4\Models\PostValues\PostValue;
use v4\Models\Translation;
use Illuminate\Support\Facades\DB;

class PostController extends V4Controller
{
    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $post = Post::with('translations')->find($id);
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


    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return PostResource|JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $authorizer = service('authorizer.post');
        // if there's no user the guards will kick them off already, but if there
        // is one we need to check the authorizer to ensure we don't let
        // users without admin perms create forms etc
        // this is an unfortunate problem with using an old version of lumen
        // that doesn't let me do guest user checks without adding more risk.
        $user = $authorizer->getUser();
        if ($user) {
            $this->authorize('store', Post::class);
        }

        // Check post permissions
        $input = $request->input();
        $post_values = $input['post_content'];

        $input['slug'] = Post::makeSlug($input['slug'] ?? $input['title']);
        $post = new Post();
        $id = null;
        if (!$post->validate($input)) {
            return self::make422($post->errors);
        }
        DB::beginTransaction();
        try {
            $post = Post::create(
                array_merge(
                    $input
                )
            );
            if (isset($input['completed_stages'])) {
                $this->savePostStages($post, $input['completed_stages']);
            }
            $this->savePostValues($post, $post_values, $post->id);
            $this->saveTranslations($request->input('translations'), $post->id, 'post');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return self::make500($e->getMessage());
        }
        return new PostResource($post);
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

        $input = $request->input();
        if (!$post->validate($input)) {
            return self::make422($post->errors);
        }
        DB::beginTransaction();
        try {
            $post->update($request->input());
            $this->updateTranslations($request->input('translations'), $post->id, 'post');
            DB::commit();
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

    protected function savePostValues(Post $post, array $post_content, int $post_id)
    {
        foreach ($post_content as $stage) {
            if (!isset($stage['fields'])) {
                continue;
            }
            foreach ($stage['fields'] as $field) {
                if (!isset($field['value'])) {
                    continue;
                }
                $value = $field['value'];
                $type = $field['type'];

                if ($type === 'tags') {
                    $type = $type === 'tags' ? 'tag' : $type;
                    $this->savePostTags($post, $field['id'], $field['value']);
                } else {
                    $class_name = "v4\Models\PostValues\Post" . ucfirst($type);
                    if (!class_exists($class_name)) {
                        throw new \Exception("Type '$type' is invalid.");
                    }
                    $post_value = new $class_name();

                    if ($type === 'point') {
                        $value = \DB::raw("GeomFromText('POINT({$value['lat']} {$value['lon']})')");
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
                        $field_value = get_class($post_value)::create($data);
                        $this->saveTranslations($field['translations'], $field_value->id, "post_value_$type");
                    }
                }
            }
        }
    }
    protected function savePostTags($post, $attr_id, $tags)
    {
        $post->valuesPostTag()->delete();
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
     * @param  $input
     * @param  $translatable_id
     * @param  $type
     * @return boolean
     */
    private function saveTranslations($input, int $translatable_id, string $type)
    {
        if (!is_array($input)) {
            return true;
        }

        foreach ($input as $language => $translations) {
            foreach ($translations as $key => $translated) {
                if (is_array($translated)) {
                    $translated = json_encode($translated);
                }

                $result = Translation::create(
                    [
                        'translatable_type' => $type,
                        'translatable_id'   => $translatable_id,
                        'translated_key'    => $key,
                        'translation'       => $translated,
                        'language'          => $language,
                    ]
                );
            }
        }
    }//end saveTranslations()


    /**
     * @param  $input
     * @param  $translatable_id
     * @param  $type
     * @return boolean
     */
    private function updateTranslations($input, int $translatable_id, string $type)
    {
        if (!is_array($input)) {
            return true;
        }

        Translation::where('translatable_id', $translatable_id)->where('translatable_type', $type)->delete();
        foreach ($input as $language => $translations) {
            foreach ($translations as $key => $translated) {
                if (is_array($translated)) {
                    $translated = json_encode($translated);
                }

                Translation::create(
                    [
                        'translatable_type' => $type,
                        'translatable_id'   => $translatable_id,
                        'translated_key'    => $key,
                        'translation'       => $translated,
                        'language'          => $language,
                    ]
                );
            }
        }
    }//end updateTranslations()


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
