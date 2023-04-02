<?php

namespace Ushahidi\Modules\V5\Http\Resources\Datasource;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DataSourceCollection extends ResourceCollection
{
    public static $wrap = 'results';
    public $collects = DataSourceResource::class;

    public function toArray($request)
    {
        return $this->collection->all();
    }
}
