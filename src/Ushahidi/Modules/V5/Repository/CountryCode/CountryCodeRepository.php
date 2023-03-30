<?php

namespace Ushahidi\Modules\V5\Repository\CountryCode;

use Ushahidi\Modules\V5\Models\CountryCode;

interface CountryCodeRepository
{
    /**
     * This method will return count of all country codes.
     * @return int
     */
    public function getCount(): int;

    /**
     * This method will fetch all the country codes from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param ?int $limit
     * @param ?int $skip
     * @param ?string $sortBy
     * @param ?string $direction
     * @return CountryCode[]
     */
    public function fetch(
        ?int $limit = null,
        ?int $skip = null,
        ?string $sortBy = null,
        ?string $direction = null
    ): array;

    /**
     * This method will fetch a single country code from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return CountryCode
     * @throws \Exception
     */
    public function findById(int $id): CountryCode;
}
