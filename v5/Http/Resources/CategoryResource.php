<?php
namespace v5\Http\Resources;

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
            'role' => $this->makeRole($this->role),
            'priority' => $this->priority,
            'children' => $this->makeChildren($this->parent, $this->children),
            'parent' => $this->makeParent($this->parent, $this->children),
            'translations' => new TranslationCollection($this->translations),
            'enabled_languages' => [
                'default'=> $this->base_language,
                'available' => $this->translations->groupBy('language')->keys()
            ]
        ];
    }

    private function makeRole($role)
    {
        return ($role === 'null' || (is_array($role) && empty($role))) ? null : $role;
    }

    private function makeChildren($parent, $children)
    {
        // not having a parent means they are a parent.... I know, I know.
        if (!$parent) {
            return new CategoryCollection($children);
        }
        return [];
    }

    private function makeParent($parent, $children)
    {
        // not having a parent means they are a parent.... I know, I know.
        if (!$parent) {
            return null;
        }
        return [
            'id' => $parent->id,
            'parent_id' => null,
            'tag' =>  $parent->tag,
            'slug' =>  $parent->slug,
            'type' =>  $parent->type,
            'color' =>  $parent->color,
            'icon' =>  $parent->icon,
            'description' => $parent->description,
            'role' =>  $this->makeRole($parent->role),
            'priority' =>  $parent->priority,
            'parent' => null,
            'translations' => new TranslationCollection($parent->translations),
            'enabled_languages' => [
                'default'=>  $parent->base_language,
                'available' =>  $parent->translations->groupBy('language')->keys()
            ]
        ];
    }
}
