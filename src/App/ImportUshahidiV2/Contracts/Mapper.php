<?php

namespace Ushahidi\App\ImportUshahidiV2\Contracts;

use Ushahidi\App\ImportUshahidiV2\Import;

interface Mapper
{
    /**
     * Map source array to Entity
     *
     * @param  Import $import   Import to scope any relation mappings
     * @param  array  $input    Source data
     * @return Entity or null if the mapping is not possible
     */
    public function __invoke(Import $import, array $input) : ?array;
}
