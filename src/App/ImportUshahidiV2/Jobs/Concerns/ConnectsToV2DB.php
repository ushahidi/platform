<?php

namespace Ushahidi\App\ImportUshahidiV2\Jobs\Concerns;

use Illuminate\Support\Facades\DB;

trait ConnectsToV2DB
{

    protected $dbConfig;
    protected $dbConnection;

    protected function getConnection()
    {
        if (!$this->dbConnection) {
            // Configure database
            config(['database.connections.importv2' => $this->dbConfig]);

            return $this->dbConnection = DB::connection('importv2');
        }

        return $this->dbConnection;
    }
}
