<?php

namespace Ushahidi\App\Jobs;

use Illuminate\Support\Facades\Log;
use Ushahidi\Contracts\Repository\Entity\ConfigRepository;
use Ushahidi\Contracts\Repository\Entity\ExportBatchRepository;
use Ushahidi\App\Multisite\MultisiteAwareJob;

class TestMultisiteJob extends Job
{
    use MultisiteAwareJob;

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
    public function handle(\Ushahidi\App\Multisite\MultisiteManager $multisite)
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
