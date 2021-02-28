<?php
namespace v5\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ForbiddenCategoryResource extends CategoryResource
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
            '_ush_hidden' => true
        ];
    }
}
