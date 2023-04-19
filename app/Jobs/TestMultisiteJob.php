<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Ushahidi\Core\Concerns\SiteAware;
use Ushahidi\Multisite\MultisiteManager;
use Ushahidi\Core\Entity\ConfigRepository;
use Ushahidi\Core\Entity\ExportBatchRepository;

class TestMultisiteJob extends Job
{
    use SiteAware;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MultisiteManager $multisite)
    {
        // Get deployment ID
        Log::debug('Site', [$multisite->getSite()]);

        // Get config
        Log::debug(
            'Site config',
            [app(ConfigRepository::class)->get('site')->asArray()]
        );

        // Get an ohanzee DB connection
        // Get an illuminate DB connection
        Log::debug(
            'Export batch',
            [app(ExportBatchRepository::class)->getByJobId(10)]
        );
    }
}
