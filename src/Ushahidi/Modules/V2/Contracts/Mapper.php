<?php

namespace Ushahidi\Modules\V2\Contracts;

use Ushahidi\Modules\V2\Import;

interface Mapper
{
    /**
     * Map source array to Entity
     *
     * @param  Import $import   Import to scope any relation mappings
     * @param  array  $input    Source data
     * @return \Ushahidi\Contracts\Entity|array|null null if the mapping is not possible
     */
    public function __invoke(Import $import, array $input) : ?array;
}
