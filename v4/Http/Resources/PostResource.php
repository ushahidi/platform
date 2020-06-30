<?php
namespace v4\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;
use Ushahidi\Core\Entity\Post;

class PostResource extends Resource
{
    public static $wrap = 'result';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $values = $this->getPostValues();
        $col = new Collection(['values' => $values, 'tasks' => $this->survey->tasks]);
        $no_values = false;

        if ($values->count() === 0) {
            $no_values = true;
        }
        $authorizer = service('authorizer.post');
        $entity = new Post($this->resource->toArray);
        // if there's no user the guards will kick them off already, but if there
        // is one we need to check the authorizer to ensure we don't let
        // users without admin perms create forms etc
        // this is an unfortunate problem with using an old version of lumen
        // that doesn't let me do guest user checks without adding more risk.
        $privileges = $authorizer->getAllowedPrivs($entity);

        return [
            'id' => $this->id,
            'form_id' => $this->form_id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'author_email' => $this->author_email,
            'author_realname' => $this->author_realname,
            'status' => $this->status,
            'published_to' => $this->published_to,
            'locale' => $this->locale,
            'created' => $this->created,
            'updated' => $this->updated,
            'post_date' => $this->post_date,
            'base_language' => $this->base_language,
            'categories' => $this->categories,
            'completed_stages' => $this->postStages,
            'post_content' => $no_values ? new TaskCollection($this->survey->tasks) : new PostValueCollection($col),
            'translations' => new TranslationCollection($this->translations),
            'enabled_languages' => [
                'default'=> $this->base_language,
                'available' => $this->translations->groupBy('language')->keys()
            ],
            'allowed_privileges' => $privileges
        ];
    }
}
