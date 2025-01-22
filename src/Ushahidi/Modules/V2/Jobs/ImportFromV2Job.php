<?php

namespace Ushahidi\Modules\V2\Jobs;

use Ushahidi\Core\Tool\Job;
use Ushahidi\Modules\V2\Import;

class ImportFromV2Job extends Job
{
    use Concerns\ConnectsToV2DB;

    protected $importId;
    protected $import;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $importId)
    {
        $this->importId = $importId;
    }

    protected function getImport() : Import
    {
        if ($this->import === null) {
            $this->import = Import::find($this->importId);
        }
        return $this->import;
    }
}
