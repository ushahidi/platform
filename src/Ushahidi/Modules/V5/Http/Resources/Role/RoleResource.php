<?php
namespace Ushahidi\Modules\V5\Http\Resources\Role;

use Illuminate\Http\Resources\Json\Resource;
use Ushahidi\Modules\V5\Http\Resources\RequestCachedResource;
use Ushahidi\Modules\V5\Http\Resources\Permissions\PermissionsCollection;
use Ushahidi\Modules\V5\Actions\Permissions\Queries\FetchPermissionsByIdQuery;
use Illuminate\Support\Collection;

use App\Bus\Query\QueryBus;

class RoleResource extends Resource
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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'display_name'=> $this->display_name,
            'protected'=> $this->protected,
            'permissions' =>$this->getResourcePermissions($this->permissions)
            

        ];
    }

    private function getResourcePermissions(Collection $permissions)
    {
        $permissions_name = [];
        foreach ($permissions->all() as $permission) {
            $permissions_name[] = $permission->permission;
        }
        return $permissions_name;
    }
}
