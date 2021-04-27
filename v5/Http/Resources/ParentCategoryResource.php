<?php
namespace v5\Http\Resources;

class ParentCategoryResource extends CategoryResource
{
    public $cache_name = 'ParentCategory';

    public function toArray($request)
    {
        // Preload key relations
        $this->resource->loadMissing(['translations']);
        return [
            'id' => $this->id,
            'parent_id' => null,
            'tag' =>  $this->tag,
            'slug' =>  $this->slug,
            'type' =>  $this->type,
            'color' =>  $this->color,
            'icon' =>  $this->icon,
            'description' => $this->description,
            'role' =>  $this->makeRole($this->role),
            'priority' =>  $this->priority,
            'parent' => null,
            'translations' => new TranslationCollection($this->translations),
            'enabled_languages' => [
                'default'=>  $this->base_language,
                'available' =>  $this->translations->groupBy('language')->keys()
            ]
        ];
    }
}
