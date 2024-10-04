<?php

namespace Ushahidi\Modules\V5\Actions\Post;

use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Contracts\Sources;
use Ushahidi\Modules\V5\Http\Resources\LockCollection;
use Ushahidi\Modules\V5\Models\Contact;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Http\Resources\PostValueCollection;

trait HandlePostOnlyParameters
{
    public function addHydrateRelationships(Post $post, array $hydrates)
    {
        foreach ($hydrates as $hydrate) {
            switch ($hydrate) {
                case 'color':
                    $post->color = $post->survey ? $post->survey->color : null;
                    break;
                case 'categories':
                    // $result['categories'] = $post->categories;
                    break;
                case 'completed_stages':
                    $post->completed_stages = $post->postStages;

                    break;
                case 'post_content':
                    $post->post_content = $this->getResourcePostContent($post);
                    break;
                case 'translations':
                    break;
                case 'contact':
                    $post->contact = null;
                    if ($post->source === Sources::WHATSAPP) {
                        if ($this->userHasManagePostPermissions()) {
                            if (isset($post->metadata['contact'])) {
                                $post->contact = (new Contact)->fill($post->metadata['contact']);
                            }
                        } else {
                            if (isset($post->metadata['contact']['id'])) {
                                $post->contact = (new Contact)->fill(['id'=>$post->metadata['contact']['id']]);
                            }
                        }
                    }
                    if ($post->message) {
                        if ($this->userHasManagePostPermissions()) {
                            $post->contact = $post->message->contact;
                        } else {
                            $post->contact = $post->message->contact->setVisible(["id"]);
                        }
                    }
                    break;
                case 'locks':
                    $post->locks = new LockCollection($post->locks);
                    break;

                case 'source':
                    $message = $post->message;
                    $post->source = $post->source ?? ($message && isset($message->type)
                        ? $message->type
                        : Post::DEFAULT_SOURCE_TYPE);

                    break;

                case 'data_source_message_id':
                    $post->data_source_message_id = null;
                    $message = $post->message;
                    if ($message) {
                        $post->data_source_message_id = $message->data_source_message_id ?? null;
                    }
                    break;
                case 'message':
                    if ($post->message && !$this->userHasManagePostPermissions()) {
                        $post->message->makeHidden("contact");
                    }
                    break;
                case 'enabled_languages':
                    $post->enabled_languages = [
                        'default' => $post->base_language,
                        'available' => $post->translations->groupBy('language')->keys()
                    ];
                    $relations['enabled_languages'] = true;
                    break;
            }
        }
        return $post;
    }
    public function hideFieldsUsedByRelationships(Post $post, array $fields = [])
    {
        foreach ($fields as $field) {
            $post->offsetUnset($field);
        }
        $post->offsetUnset('values_int');

        return $post;
    }
    public function hideUnwantedRelationships(Post $post, array $hydrates)
    {
        // hide  post_content relationships
        $post->makeHidden('survey');
        $post->makeHidden('valuesVarchar');
        $post->makeHidden('valuesInt');
        $post->makeHidden('valuesText');
        $post->makeHidden('valuesDatetime');
        $post->makeHidden('valuesDecimal');
        $post->makeHidden('valuesDecimal');
        $post->makeHidden('valuesGeometry');
        $post->makeHidden('valuesMarkdown');
        $post->makeHidden('valuesMedia');
        $post->makeHidden('valuesPoint');
        $post->makeHidden('valuesRelation');
        $post->makeHidden('valuesPostsMedia');
        $post->makeHidden('valuesPostsSet');
        $post->makeHidden('valuesPostTag');

        // hide source relationships
        if (!in_array('message', $hydrates)) {
            $post->makeHidden('message');
        }

        // hide completed_stages relationships
        $post->makeHidden('postStages');


        return $post;
    }

    private function getResourcePostContent($post)
    {
        $values = $post->getPostValues(); // Calling method on Post Model
        $no_values = $values->count() === 0 ? true : false;
        $col = new Collection([
            'values' => $values,
            'tasks' => $post->survey ? $post->survey->tasks : []
        ]);
        if ($post->survey) {
            $post_content = new PostValueCollection($col);
        } else {
            $post_content = Collection::make([]);
        }
        return $post_content;
    }
}
