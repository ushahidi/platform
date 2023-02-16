<?php
namespace Ushahidi\Modules\V5\Http\Resources\Collection;

use Illuminate\Http\Resources\Json\Resource;

class CollectionResource extends Resource
{


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
            'role' => $this->role,
            'view' => $this->view,
            'view_options' => $this->view_options,
            'featured' => $this->featured,
            'created' => $this->created,
            'updated' => $this->updated
        ];
    }
}
