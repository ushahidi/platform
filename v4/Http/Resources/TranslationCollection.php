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

        $grouped = $this->collection->mapToGroups(function ($item, $key) {
            return [$item->language => $item];
        });
        $combined = $grouped->map(function ($item, $key) {
            return $item->mapWithKeys(function($i) {
                return [$i->translated_key => $i->translation ];
            });
        });
        return $combined;
    }
}
