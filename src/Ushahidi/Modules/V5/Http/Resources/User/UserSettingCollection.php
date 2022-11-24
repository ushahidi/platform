<?php


namespace Ushahidi\Modules\V5\Http\Resources\User;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserSettingCollection extends ResourceCollection
{
    public static $wrap = 'data';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = 'Ushahidi\Modules\V5\Http\Resources\User\UserSettingResource';
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
