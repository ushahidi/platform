<?php
namespace v5\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use v5\Models\PostValues\PostValue;

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
        $value = $this->value;
        $value_translations = $this->translations;

        return [
            'id' => $this->id,
            'post_id' => $this->post_id,
            'value' => $value,
            'form_attribute_id' => $this->form_attribute_id,
            'created' => $this->created,
            'translations' => $this->translations ? new TranslationCollection($value_translations) : [],
            'translatable_fields' => PostValue::translatableAttributes()
        ];
    }
}
