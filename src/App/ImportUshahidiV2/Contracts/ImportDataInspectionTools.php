<?php

namespace Ushahidi\App\ImportUshahidiV2\Contracts;

use Illuminate\Support\Collection;

interface ImportDataInspectionTools
{
    /**
     * Analyzes v2 numeric field responses and suggests which type of
     * v3+ field can be used to store the data.
     *
     * Returns "varchar", "decimal" or "int"
     */
    public function suggestNumberStorage(int $fieldId): string;
}
