<?php

namespace Ushahidi\Modules\V5\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class CountryCodeResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource['id'],
            'countryName' => $this->resource['country_name'],
            'dialCode' => $this->resource['dial_code'],
            'countryCode' => $this->resource['country_code'],
        ];
    }
}
