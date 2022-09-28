<?php


namespace Ushahidi\Modules\V5\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DataSourceCollection extends ResourceCollection
{
    public static $wrap = 'results';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = DataSourceResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->all();
    }
}
