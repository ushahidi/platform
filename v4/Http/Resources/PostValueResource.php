<?php
namespace v4\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PostValueResource extends Resource
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
            'post_id' => $this->post_id,
            'value' => $this->value,
            'form_attribute_id' => $this->slug,
            'created' => $this->created,
            'translations' => new TranslationCollection($this->translations)
        ];
    }
}
