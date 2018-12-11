<?php

namespace Ushahidi\App\Repository\Concerns;

use Ohanzee\DB;
use Ohanzee\Database;

trait UsesBulkAutoIncrement
{

    protected function checkAutoIncMode()
    {
        // Check MySQL `innodb_autoinc_lock_mode` = 0 or 1 before running
        $lockMode = DB::query(Database::SELECT, "SHOW VARIABLES LIKE 'innodb_autoinc_lock_mode'")
            ->execute($this->db())
            ->get('Value');

        if (!in_array((int) $lockMode, [0, 1])) {
            throw new \RuntimeException('Cannot bulk insert users with innodb_autoinc_lock_mode = ' . $lockMode);
        }
    }
}
