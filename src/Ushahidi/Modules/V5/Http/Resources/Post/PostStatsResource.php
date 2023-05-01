<?php

namespace Ushahidi\Modules\V5\Http\Resources\Post;

use Ushahidi\Modules\V5\Http\Resources\BaseResource;

class PostStatsResource extends BaseResource
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
        $data = $this->resource->toArray();
        return $data;
    }
}
