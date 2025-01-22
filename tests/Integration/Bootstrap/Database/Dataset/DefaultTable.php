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

class DefaultTable extends AbstractTable
{

    public function __construct($tableMetaData)
    {
        $this->setTableMetaData($tableMetaData);
        $this->data = [];
    }

    public function addRow($values = []): void
    {
        $this->data[] = \array_replace(
            \array_fill_keys($this->getTableMetaData()->getColumns(), null),
            $values
        );
    }

    public function addTableRows($table): void
    {
        $tableColumns = $this->getTableMetaData()->getColumns();
        $rowCount     = $table->getRowCount();

        for ($i = 0; $i < $rowCount; $i++) {
            $newRow = [];

            foreach ($tableColumns as $columnName) {
                $newRow[$columnName] = $table->getValue($i, $columnName);
            }
            $this->addRow($newRow);
        }
    }

    public function setValue($row, $column, $value): void
    {
        if (isset($this->data[$row])) {
            $this->data[$row][$column] = $value;
        } else {
            throw new \Exception('The row given does not exist.');
        }
    }
}
