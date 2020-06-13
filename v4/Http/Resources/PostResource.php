<?php
namespace v4\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

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
            'survey' => $this->survey,
            'post_content' => $this->values(),
            'translations' => new TranslationCollection($this->translations),
            'enabled_languages' => [
                'default'=> $this->base_language,
                'available' => $this->translations->groupBy('language')->keys()
            ]
        ];
    }
}
