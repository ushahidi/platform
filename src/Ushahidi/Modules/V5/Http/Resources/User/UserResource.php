<?php
namespace Ushahidi\Modules\V5\Http\Resources\User;

use Illuminate\Http\Resources\Json\Resource;
use Ushahidi\Modules\V5\Http\Resources\RequestCachedResource;
use Illuminate\Support\Collection;
use Ushahidi\Core\Entity\User;

use App\Bus\Query\QueryBus;

class UserResource extends Resource
{

    use RequestCachedResource;

    public static $wrap = 'data';
    private function getResourcePrivileges()
    {
        $authorizer = service('authorizer.user');
        // Obtain v3 entity from the v5 post model
        // Note that we use attributesToArray instead of toArray because the first
        // would have the effect of causing unnecessary requests to the database
        // (relations are not needed in this case by the authorizer)
        $entity = new User($this->resource->attributesToArray());
        // if there's no user the guards will kick them off already, but if there
        // is one we need to check the authorizer to ensure we don't let
        // users without admin perms create forms etc
        // this is an unfortunate problem with using an old version of lumen
        // that doesn't let me do guest user checks without adding more risk.
        return $authorizer->getAllowedPrivs($entity);
    }

    private function getGravatar($email)
    {
        return $email ?
            md5(strtolower(trim($email))) :
            '00000000000000000000000000000000';
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
            'realname' => $this->realname,
            'email' => $this->email,
            'role' => $this->role,
            'language' => $this->language,
            'created' => $this->created,
            'updated' => $this->updated,
            'logins' => $this->logins,
            'failed_attempts' => $this->failed_attempts,
            'last_login' => $this->last_login,
            'last_attempt' => $this->last_attempt,
            'gravatar' => $this->getGravatar($this->email),
            'contacts' => [],
            'permissions' => $this->getResourcePermissions($this->getPermission()),
            'allowed_privileges' => $this->getResourcePrivileges()



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
