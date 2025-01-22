<?php
namespace Ushahidi\Modules\V5\Http\Resources\Permissions;

use Illuminate\Http\Resources\Json\JsonResource as Resource;
use Ushahidi\Modules\V5\Http\Resources\RequestCachedResource;
use Ushahidi\Core\Entity\Permission;

class PermissionsResource extends Resource
{

    use RequestCachedResource;

    public static $wrap = 'result';


    private function getResourcePrivileges()
    {
        $authorizer = service('authorizer.permission');
        // Obtain v3 entity from the v5 post model
        // Note that we use attributesToArray instead of toArray because the first
        // would have the effect of causing unnecessary requests to the database
        // (relations are not needed in this case by the authorizer)
        $entity = new Permission($this->resource->attributesToArray());
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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'allowed_privileges' => $this->getResourcePrivileges()
        ];
    }
}
