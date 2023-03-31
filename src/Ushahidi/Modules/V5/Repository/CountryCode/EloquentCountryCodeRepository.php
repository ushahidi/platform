<?php

namespace Ushahidi\Modules\V5\Repository\CountryCode;

use Ushahidi\Modules\V5\Models\CountryCode;

final class EloquentCountryCodeRepository implements CountryCodeRepository
{
    public function getCount(): int
    {
        return CountryCode::count();
    }

    public function fetch(
        ?int $limit = null,
        ?int $skip = null,
        ?string $sortBy = null,
        ?string $direction = null
    ): array {
        $query = CountryCode::query();

        if ($sortBy) {
            $query->orderBy($sortBy, $direction ?? 'asc');
        }

        if ($limit) {
            $query->take($limit);
        }

        if ($skip) {
            $query->skip($skip);
        }

        return $query->get()->toArray();
    }

    public function findById(int $id): CountryCode
    {
        $countryCode = CountryCode::where('id', $id)->first();

        if (!$countryCode instanceof CountryCode) {
            throw new \Exception('Country code not found');
        }

        return $countryCode;
    }
}
