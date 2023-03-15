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

abstract class AbstractTableMetadata
{
    protected $columns;

    protected $primaryKeys;

    protected $tableName;

    public function getColumns()
    {
        return $this->columns;
    }

    public function getPrimaryKeys()
    {
        return $this->primaryKeys;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function matches($other)
    {
        if ($this->getTableName() != $other->getTableName() ||
            $this->getColumns() != $other->getColumns()
        ) {
            return false;
        }

        return true;
    }
}
