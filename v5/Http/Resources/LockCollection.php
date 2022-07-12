<?php


namespace v5\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LockCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = 'v5\Http\Resources\LockResource';
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
