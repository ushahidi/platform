<?php

namespace Ushahidi\App\ImportUshahidiV2\Contracts;

use Ushahidi\Core\Entity;

interface Mapper
{
    /**
     * Map source array to Entity
     *
     * @param  int    $importId Import ID used to scope any relation mappings
     * @param  array  $input    Source data
     * @return Entity or null if the mapping is not possible
     */
    public function __invoke(int $importId, array $input) : ?Entity;
}
