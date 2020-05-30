<?php
namespace v4\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class CategoryResource extends Resource
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
            'parent_id' => $this->parent_id,
            'tag' => $this->tag,
            'slug' => $this->slug,
            'type' => $this->type,
            'color' => $this->color,
            'icon' => $this->icon,
            'description' => $this->description,
            'role' => $this->role,
            'priority' => $this->priority,
            'children' => new CategoryCollection($this->children),
            'parent' => $this->parent,
            'translations' => new TranslationCollection($this->translations),
            'enabled_languages' => [
                'default'=> $this->base_language,
                'available' => $this->translations->groupBy('language')->keys()
            ]
        ];
    }
}
