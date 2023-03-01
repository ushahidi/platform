<?php
namespace Ushahidi\Modules\V5\Http\Resources\Permissions;

use Illuminate\Http\Resources\Json\JsonResource as Resource;
use Ushahidi\Modules\V5\Http\Resources\RequestCachedResource;

class PermissionsResource extends Resource
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
            'name' => $this->name,
            'description' => $this->description
        ];
    }
}
