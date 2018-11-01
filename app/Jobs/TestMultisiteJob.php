<?php

namespace Ushahidi\App\Jobs;

use Illuminate\Support\Facades\Log;

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
        var_dump($multisite->getSiteId());

        //
        // Get config
        // Get an ohanzee DB connection
        // Get an illuminate DB connection
    }
}
