<?php
namespace Ushahidi\Modules\V5\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource as Resource;

class MediaResource extends Resource
{

    use RequestCachedResource;

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
            'media_id' => $this->value,
        ];
    }
}
