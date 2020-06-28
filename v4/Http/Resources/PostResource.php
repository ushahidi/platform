<?php
namespace v4\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;

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
        $no_values = false;

        if ($values->count() === 0) {
            $no_values = true;
        }

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
            'post_content' => $no_values ? $this->survey->tasks : new PostValueCollection($values),
            'translations' => new TranslationCollection($this->translations),
            'enabled_languages' => [
                'default'=> $this->base_language,
                'available' => $this->translations->groupBy('language')->keys()
            ]
        ];
    }
}
