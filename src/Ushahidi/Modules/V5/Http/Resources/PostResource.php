<?php

namespace Ushahidi\Modules\V5\Http\Resources;

use Illuminate\Support\Collection;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Modules\V5\Models\Post\Post as v5Post;
use Ushahidi\Modules\V5\Http\Resources\Survey\TaskCollection;
use Ushahidi\Modules\V5\Http\Resources\BaseResource;

class PostResource extends BaseResource
{
    public static $wrap = 'result';
    private const DEFAULT_SOURCE_TYPE = 'web';

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    private function includeResourceFields($request)
    {
        return self::includeFields($request, [
            'id',
            'form_id',
            'user_id',
            'type',
            'title',
            'slug',
            'content',
            'author_email',
            'author_realname',
            'status',
            'published_to',
            'locale',
            'color',
            'created',
            'updated',
            'post_date',
            'base_language' => $this->base_language,
            //            'translations' => new TranslationCollection($this->translations),
            //            'enabled_languages' => [
            //                'default'=> $this->base_language,
            //                'available' => $this->translations->groupBy('language')->keys()
            //            ],
        ]);
    }

    private function getResourcePostContent()
    {
        $values = $this->getPostValues(); // Calling method on Post Model
        $no_values = $values->count() === 0 ? true : false;
        $col = new Collection([
            'values' => $values,
            'tasks' => $this->survey ? $this->survey->tasks : []
        ]);

        if ($no_values && $this->survey) {
            $post_content = new TaskCollection($this->survey->tasks);
        } elseif ($this->survey) {
            $post_content = new PostValueCollection($col);
        } else {
            $post_content = Collection::make([]);
        }

        return $post_content;
    }

    private function getResourcePrivileges()
    {
        $authorizer = service('authorizer.post');
        // Obtain v3 entity from the v5 post model
        // Note that we use attributesToArray instead of toArray because the first
        // would have the effect of causing unnecessary requests to the database
        // (relations are not needed in this case by the authorizer)
        $entity = new Post($this->resource->attributesToArray());
        // if there's no user the guards will kick them off already, but if there
        // is one we need to check the authorizer to ensure we don't let
        // users without admin perms create forms etc
        // this is an unfortunate problem with using an old version of lumen
        // that doesn't let me do guest user checks without adding more risk.
        return $authorizer->getAllowedPrivs($entity);
    }

    private function hydrateResourceRelationships($request)
    {
        $hydrate = $this->getHydrate(v5Post::$relationships, $request);
        $result = [];
        foreach ($hydrate as $relation) {
            switch ($relation) {
                case 'categories':
                    $result['categories'] = $this->categories;
                    break;
                case 'completed_stages':
                    $result['completed_stages'] = $this->postStages;
                    break;
                case 'post_content':
                    $result['post_content'] = $this->getResourcePostContent();
                    break;
                case 'translations':
                    $result['translations'] = new TranslationCollection($this->translations);
                    break;
                case 'contact':
                    $message = $this->message;
                    if ($message) {
                        $contact = $message->contact;
                        if ($contact) {
                            $result['contact'] = new ContactPointerResource($message->contact);
                        }
                    }
                    break;
                case 'message':
                    $message = $this->message;
                    if ($message) {
                        $result['message'] = new MessagePointerResource($message);
                    }
                    break;
                case 'enabled_languages':
                    $result['enabled_languages'] = [
                        'default' => $this->base_language,
                        'available' => $this->translations->groupBy('language')->keys()
                    ];
                    break;
            }
        }
        $result['locks'] = new LockCollection($this->locks);
        return $result;
    }

    private function getPostSourceDetails()
    {
        $message = $this->message;
        $details['source'] = $this->source ?? ($message && isset($message->type)
            ? $message->type
            : self::DEFAULT_SOURCE_TYPE);
        $details['data_source_message_id'] = $message->data_source_message_id ?? null;
        return $details;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // @TODO-jan27 make translations and enabled_languages optional
        // @TODO-jan27 make id required
        $fields = $this->includeResourceFields($request);
        $result = array_merge(
            $this->setResourceFields($fields),
            $this->getPostSourceDetails()
        );
        $hydrated = $this->hydrateResourceRelationships($request);
        $allowed_privs = ['allowed_privileges' => $this->getResourcePrivileges()];
        return array_merge($result, $hydrated, $allowed_privs);
    }
}
