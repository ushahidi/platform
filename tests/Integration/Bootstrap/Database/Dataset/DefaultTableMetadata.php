<?php
/*
 * This file adapted from the DbUnit package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 * (c) Ushahidi Team <team@ushahidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ushahidi\Tests\Integration\Bootstrap\Database\Dataset;

use Ushahidi\Tests\Integration\Bootstrap\Database\Dataset\AbstractTableMetadata;

class DefaultTableMetadata extends AbstractTableMetadata
{
    public function __construct($tableName, array $columns, array $primaryKeys = [])
    {
        $this->tableName   = $tableName;
        $this->columns     = $columns;
        $this->primaryKeys = [];

        foreach ($primaryKeys as $columnName) {
            if (!\in_array($columnName, $this->columns)) {
                throw new \Exception('Primary key column passed that is not in the column list.');
            }
            $this->primaryKeys[] = $columnName;
        }
    }
}
