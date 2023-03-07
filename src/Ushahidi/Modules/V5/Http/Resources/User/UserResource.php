<?php
namespace Ushahidi\Modules\V5\Http\Resources\User;

use Illuminate\Http\Resources\Json\Resource;
use Ushahidi\Modules\V5\Http\Resources\RequestCachedResource;
use Illuminate\Support\Collection;

use App\Bus\Query\QueryBus;

class UserResource extends Resource
{

    use RequestCachedResource;

    public static $wrap = 'data';

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
            'realname' => $this->realname,
            'email' => $this->email,
            'role'=> $this->role,
            'language'=> $this->language,
            'created'=> $this->created,
            'updated'=> $this->updated,
            'logins'=> $this->logins,
            'failed_attempts'=> $this->failed_attempts,
            'last_login'=> $this->last_login,
            'last_attempt'=> $this->last_attempt,
            'permissions' =>$this->getResourcePermissions($this->getPermission())


           // 'Contacts' =>$this->Contacts
            

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
