<?php
namespace v5\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TranslationResource extends Resource
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
            'key' => $this->translated_key,
            'translation' => $this->translation,
            'language' => $this->language
        ];
    }
}
