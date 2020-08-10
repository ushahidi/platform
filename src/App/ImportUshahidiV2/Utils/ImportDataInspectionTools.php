<?php

namespace Ushahidi\App\ImportUshahidiV2\Utils;

use Ushahidi\App\ImportUshahidiV2\Contracts\ImportDataInspectionTools as ImportDataInspectionToolsContract;
use Ushahidi\App\ImportUshahidiV2\Jobs\Concerns\ConnectsToV2DB;

use Illuminate\Support\Collection;

class ImportDataInspectionTools implements ImportDataInspectionToolsContract
{

    use ConnectsToV2DB;

    public function suggestNumberStorage(int $fieldId): string
    {
        return $this->doNumberStudy($this->getFieldSamples($fieldId));
    }

    protected function getFieldSamples(int $fieldId)
    {
        return $this->getConnection()
            ->table('form_response')
            ->select('form_response')
            ->where('form_field_id', '=', $fieldId)
            ->where('form_response', '<>', '');
    }

    protected function doNumberStudy($samples) : string
    {
        // initial suggestion is integer, since that's what can
        // usually hold the most significant figures in v3+
        $suggestion = "int";

        // review samples
        foreach ($samples->cursor() as $n) {
            if ($suggestion == 'int') {
                // test the sample for integer parseability
                if (is_numeric($n)) {
                    if (!preg_match("/^-?[0-9]+$/", $n)) {
                        // a number but not an int
                        $suggestion = "decimal";
                    }
                } else {
                    return "varchar";    // stop evaluation
                }
            } else { // if ($suggestion = 'decimal') {
                if (!is_numeric($n)) {
                    return "varchar";    // stop evaluation
                }
            }
        }
    }
}
