<?php

namespace Ushahidi\Tests\Integration\Bootstrap\Database;
/*
 * This file adapted from the DbUnit package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 * (c) Ushahidi Team <team@ushahidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class FilteredDataset extends Dataset
{
    protected $tableNames;

    public function __construct($databaseConnection, array $tableNames)
    {
        parent::__construct($databaseConnection);
        $this->tableNames = $tableNames;
    }

    public function getTableNames()
    {
        return $this->tableNames;
    }
}
