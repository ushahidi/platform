<?php

namespace Ushahidi\Modules\V5\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CountryCodeCollection extends ResourceCollection
{
    /**
     * @var int
     */
    private $totalCount;

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = 'Ushahidi\Modules\V5\Http\Resources\CountryCodeResource';

    public function __construct($resource, int $count)
    {
        parent::__construct($resource);
        $this->totalCount = $count;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $data = $this->collection
            ->map(function ($countryCode) {
                return new CountryCodeResource($countryCode);
            })
            ->toArray();

        return [
            'data' => $data,
            'current_count' => $this->collection->count(),
            'total_count' => $this->getTotalCount()
        ];
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
}
