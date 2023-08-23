<?php
namespace Ushahidi\Modules\V5\Http\Resources\DataProvider;

use Illuminate\Http\Resources\Json\JsonResource as Resource;
use Ushahidi\Core\Entity\DataProvider as DataProviderEntity;


use App\Bus\Query\QueryBus;

class DataProviderResource extends Resource
{

    // use RequestCachedResource;

    public static $wrap = 'result';
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->resource;
        return $data;
    }
}
