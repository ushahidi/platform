<?php


namespace Ushahidi\Modules\V5\Http\Resources\Role;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleCollection extends ResourceCollection
{
    public static $wrap = 'data';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = 'Ushahidi\Modules\V5\Http\Resources\Role\RoleResource';
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection;
    }
}
