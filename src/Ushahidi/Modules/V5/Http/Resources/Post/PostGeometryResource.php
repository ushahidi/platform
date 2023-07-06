<?php

namespace Ushahidi\Modules\V5\Http\Resources\Post;

use Ushahidi\Modules\V5\Http\Resources\BaseResource;

class PostGeometryResource extends BaseResource
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
        if (is_a($this->resource, "stdClass")) {
            $data = (array) $this->resource;
        } else {
            $data = $this->resource->toArray();
        }
        $data['geojson'] = json_decode($data['geojson'], true);
        return $data;
    }
}
