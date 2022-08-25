<?php
namespace Ushahidi\Modules\V5\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Ushahidi\Modules\V5\Models\User as User;

class LockResource extends Resource
{
    public static $wrap = 'result';

    private function getOwnerName()
    {
        $owner = User::where('id', '=', $this->user_id)->first();
        return $owner->realname;
    }

    private function lockIsBreakable()
    {
        $authorizer = service('authorizer.post');
        $user =$authorizer->getUser();
        return $user->role ===  "admin";
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
            'user_id' => $this->user_id,
            'post_id' => $this->post_id,
            'expires' => $this->expires,
            'breakable' => $this->lockIsBreakable(),
            'owner_name' => $this->getOwnerName(),
        ];
    }
}
