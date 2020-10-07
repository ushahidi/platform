<?php

namespace Ushahidi\App\ImportUshahidiV2\Contracts;

use Illuminate\Support\Collection;

interface ImportDataTools
{
    /**
     * Obtain estimation of how well each of the possible date
     * formats work on the string data stored for V2 field with id fieldId
     *
     * Returns array where the keys are the different formats considered
     * and the values are the estimated success rate for each format
     */
    public function tryDateDecodeFormats(int $fieldId) : Array;

    /**
     * Analyzes v2 numeric field responses and suggests which type of
     * v3+ field can be used to store the data.
     *
     * Returns "varchar", "decimal" or "int"
     */
    public function suggestNumberStorage(int $fieldId): string;

    /**
     * Applies union operation on geometries given (WKT format).
     * Returns another array with a single geometry in it.
     */
    public function mergeGeometries(array $geometries): array;
}
