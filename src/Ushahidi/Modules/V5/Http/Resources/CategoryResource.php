<?php
namespace Ushahidi\Modules\V5\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource as Resource;
use Ushahidi\Core\Entity\Tag;

class CategoryResource extends Resource
{

    use RequestCachedResource;

    public static $wrap = 'result';

    private function getResourcePrivileges()
    {
        $authorizer = service('authorizer.tag');
        // Obtain v3 entity from the v5 post model
        // Note that we use attributesToArray instead of toArray because the first
        // would have the effect of causing unnecessary requests to the database
        // (relations are not needed in this case by the authorizer)
        $resource_array = $this->resource->attributesToArray();
        unset($resource_array['completed_stages']);
        $entity = new Tag($resource_array);
        // if there's no user the guards will kick them off already, but if there
        // is one we need to check the authorizer to ensure we don't let
        // users without admin perms create forms etc
        // this is an unfortunate problem with using an old version of lumen
        // that doesn't let me do guest user checks without adding more risk.
        return $authorizer->getAllowedPrivs($entity);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->resource->toArray();
        if (isset($data['role'])) {
            $data['role'] = $this->makeRole($this->role);
        }
        if (isset($data['children'])) {
            $data['children'] = $this->makeChildren($this->parent, $this->children);
        }
        if (isset($data['parent'])) {
            $data['parent'] = $this->makeRole($this->parent);
        }
        if (isset($data['translations'])) {
            $data['translations'] = (new TranslationCollection($this->translations))->toArray(null);
            $data['enabled_languages'] = [
                'default'=> $this->base_language,
                'available' => $this->translations->groupBy('language')->keys()
            ];
        }
        // To Do: this call cause an infinit loop , need to be checked
        // note: it was not found before last commit , so it will not disable it it will not affect the work of front end
        //$data['allowed_privileges']=  $this->getResourcePrivileges();
        return $data;
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
