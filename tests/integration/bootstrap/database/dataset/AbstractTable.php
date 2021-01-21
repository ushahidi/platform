<?php

namespace Tests\Integration\Bootstrap\Database\DataSet;

use SimpleXMLElement;

class AbstractTable
{
    /**
     * @var ITableMetadata
     */
    protected $tableMetaData;

    protected $data;

    private $other;

    public function __toString()
    {
        $columns = $this->getTableMetaData()->getColumns();
        $count   = \count($columns);

        // if count less than 0 (when table is empty), then set count to 1
        $count         = $count >= 1 ? $count : 1;
        $lineSeparator = \str_repeat('+----------------------', $count) . "+\n";
        $lineLength    = \strlen($lineSeparator) - 1;

        $tableString = $lineSeparator;
        $tblName     = $this->getTableMetaData()->getTableName();
        $tableString .= '| ' . \str_pad(
                $tblName,
                $lineLength - 4,
                ' ',
                STR_PAD_RIGHT
            ) . " |\n";
        $tableString .= $lineSeparator;
        $rows = $this->rowToString($columns);
        $tableString .= !empty($rows) ? $rows . $lineSeparator : '';

        $rowCount = $this->getRowCount();

        for ($i = 0; $i < $rowCount; $i++) {
            $values = [];

            foreach ($columns as $columnName) {
                if ($this->other) {
                    try {
                        if ($this->getValue($i, $columnName) != $this->other->getValue($i, $columnName)) {
                            $values[] = \sprintf(
                                '%s != actual %s',
                                \var_export($this->getValue($i, $columnName), true),
                                \var_export($this->other->getValue($i, $columnName), true)
                            );
                        } else {
                            $values[] = $this->getValue($i, $columnName);
                        }
                    } catch (\Exception $ex) {
                        $values[] = $this->getValue($i, $columnName) . ': no row';
                    }
                } else {
                    $values[] = $this->getValue($i, $columnName);
                }
            }

            $tableString .= $this->rowToString($values) . $lineSeparator;
        }

        return ($this->other ? '(table diff enabled)' : '') . "\n" . $tableString . "\n";
    }

    public function getTableMetaData()
    {
        return $this->tableMetaData;
    }

    public function getRowCount()
    {
        return \count($this->data);
    }

    public function getValue($row, $column)
    {
        if (isset($this->data[$row][$column])) {
            $value = $this->data[$row][$column];

            return ($value instanceof SimpleXMLElement) ? (string) $value : $value;
        }

        if (!\in_array($column, $this->getTableMetaData()->getColumns()) || $this->getRowCount() <= $row) {
            throw new \Exception("The given row ({$row}) and column ({$column}) do not exist in table {$this->getTableMetaData()->getTableName()}");
        }

        return;
    }

    public function getRow($row)
    {
        if (isset($this->data[$row])) {
            return $this->data[$row];
        }

        if ($this->getRowCount() <= $row) {
            throw new \Exception("The given row ({$row}) does not exist in table {$this->getTableMetaData()->getTableName()}");
        }

        return;
    }

    public function matches($other)
    {
        $thisMetaData  = $this->getTableMetaData();
        $otherMetaData = $other->getTableMetaData();

        if (!$thisMetaData->matches($otherMetaData) ||
            $this->getRowCount() != $other->getRowCount()
        ) {
            return false;
        }

        $columns  = $thisMetaData->getColumns();
        $rowCount = $this->getRowCount();

        for ($i = 0; $i < $rowCount; $i++) {
            foreach ($columns as $columnName) {
                $thisValue  = $this->getValue($i, $columnName);
                $otherValue = $other->getValue($i, $columnName);

                if (\is_numeric($thisValue) && \is_numeric($otherValue)) {
                    if ($thisValue != $otherValue) {
                        $this->other = $other;

                        return false;
                    }
                } elseif ($thisValue !== $otherValue) {
                    $this->other = $other;

                    return false;
                }
            }
        }

        return true;
    }

    public function assertContainsRow(array $row)
    {
        return \in_array($row, $this->data);
    }

    protected function setTableMetaData($tableMetaData): void
    {
        $this->tableMetaData = $tableMetaData;
    }

    protected function rowToString(array $row)
    {
        $rowString = '';

        foreach ($row as $value) {
            if (null === $value) {
                $value = 'NULL';
            }

            $value_str = \mb_substr($value, 0, 20);

            $correction = \strlen($value_str) - \mb_strlen($value_str);
            $rowString .= '| ' . \str_pad($value_str, 20 + $correction, ' ', STR_PAD_BOTH) . ' ';
        }

        $rowString = !empty($row) ? $rowString . "|\n" : '';

        return $rowString;
    }
}