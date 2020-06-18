<?php


namespace v4\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TranslationCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = 'v4\Http\Resources\TranslationResource';
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // translate options
        // use key "options" for type "field" to do json_decode
        $grouped = $this->collection->mapToGroups(function ($item, $key) {
            $is_json_field = $item->translated_key === 'options' &&
                $item->translatable_type==='field';
            $is_point_field = $item->translated_key === 'value' &&
                $item->translatable_type==='post_value_point';
            if ($is_json_field || $is_point_field) {
                $item->translation = json_decode($item->translation);
            }
            return [$item->language => $item];
        });
        $combined = $grouped->map(function ($item, $key) {
            return $item->mapWithKeys(function ($i) {
                return [$i->translated_key => $i->translation ];
            });
        });
        return $combined;
    }
}
