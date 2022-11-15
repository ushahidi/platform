<?php


namespace Ushahidi\Modules\V5\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    public static $wrap = 'results';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = 'Ushahidi\Modules\V5\Http\Resources\PostResource';
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'results' => $this->collection,
            'count' => $this->count()
        ];
    }

    public function count()
    {
        return count($this->collection);
    }
}
