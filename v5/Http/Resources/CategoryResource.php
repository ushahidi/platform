<?php
namespace v5\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{

    use RequestCachedResource;

    public static $wrap = 'result';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Preload key relations
        $this->resource->loadMissing(['parent', 'children', 'translations']);
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
            'parent' => $this->makeParent($this->parent),
            'translations' => new TranslationCollection($this->translations),
            'enabled_languages' => [
                'default'=> $this->base_language,
                'available' => $this->translations->groupBy('language')->keys()
            ]
        ];
    }

    protected function makeRole($role)
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

    private function makeParent($parent)
    {
        // not having a parent means they are a parent.... I know, I know.
        if (!$parent) {
            return null;
        }
        return ParentCategoryResource::make($parent);
    }
}
