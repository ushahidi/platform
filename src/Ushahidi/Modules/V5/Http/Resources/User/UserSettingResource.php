<?php
namespace Ushahidi\Modules\V5\Http\Resources\User;

use Illuminate\Http\Resources\Json\Resource;
use Ushahidi\Modules\V5\Http\Resources\RequestCachedResource;
use Illuminate\Support\Collection;

class UserSettingResource extends Resource
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
            'config_key' => $this->config_key,
            'config_value' => $this->config_value,
            'user_id'=> $this->user_id,
            'created'=> $this->created,
            'updated' =>$this->updated
        ];
    }
}
