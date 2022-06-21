<?php

namespace Ushahidi\App\ImportUshahidiV2\Jobs;

use Illuminate\Support\Facades\Log;
use Ushahidi\App\Jobs\Job;
use Ushahidi\App\ImportUshahidiV2\ManifestSchemas\ImportParameters;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportRepository;

class SetupImport extends ImportUshahidiV2Job
{
    protected $dbConfig;
    protected $extraParams;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $importId, array $dbConfig, ImportParameters $extraParams)
    {
        parent::__construct($importId);
        $this->dbConfig = $dbConfig;
        $this->extraParams = $extraParams;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ImportRepository $importRepo)
    {
        // Collect import job metadata
        $metadata = [];

        if ($this->extraParams !== null) {
            $metadata['parameters'] = (object) $this->extraParams;
        }
        $metadata['v2_settings'] = $this->parseV2Settings();

        $this->getImport()->metadata = $metadata;
        $importRepo->update($this->getImport());

        $this->runChecks();
    }

    /**
     * parse v2 settings into a settings object
     */
    protected function parseV2Settings()
    {
        $settings = [];
        $cursor = $this->getConnection()->table('settings')->cursor();
        foreach ($cursor as $row) {
            $settings[$row->key] = $row->value;
        }

        return $settings;
    }

    /**
     * Run a few checks on the import setup
     */
    protected function runChecks()
    {
        // Timezone
        $tz = $this->getImport()->getImportTimezone();
        if (!$tz) {
            Log::warning('No timezone configured, will use current host timezone to interpret source dates');
        }
        if (!in_array($tz, \DateTimeZone::listIdentifiers())) {
            throw new \Exception('Invalid timezone setting: ' . $tz . '. Bailing out.');
        }
    }
}
