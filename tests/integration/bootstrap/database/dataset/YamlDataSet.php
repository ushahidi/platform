<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Integration\Bootstrap\Database\DataSet;

/**
 * Creates YamlDataSets.
 *
 * You can incrementally add YAML files as tables to your datasets
 */
class YamlDataSet extends AbstractDataSet
{
    /**
     * @var array
     */
    protected $tables = [];

    /**
     * @var IYamlParser
     */
    protected $parser;

    /**
     * Creates a new YAML dataset
     *
     * @param string      $yamlFile
     * @param IYamlParser $parser
     */
    public function __construct($yamlFile, $parser = null)
    {
        if ($parser == null) {
            $parser = new SymfonyYamlParser();
        }
        $this->parser = $parser;
        $this->addYamlFile($yamlFile);
    }

    /**
     * Adds a new yaml file to the dataset.
     *
     * @param string $yamlFile
     */
    public function addYamlFile($yamlFile): void
    {
        $data = $this->parser->parseYaml($yamlFile);

        foreach ($data as $tableName => $rows) {
            if (!isset($rows)) {
                $rows = [];
            }

            if (!\is_array($rows)) {
                continue;
            }

            if (!\array_key_exists($tableName, $this->tables)) {
                $columns = $this->getColumns($rows);

                $tableMetaData = new DefaultTableMetadata(
                    $tableName,
                    $columns
                );

                $this->tables[$tableName] = new DefaultTable(
                    $tableMetaData
                );
            }

            foreach ($rows as $row) {
                $this->tables[$tableName]->addRow($row);
            }
        }
    }

    /**
     * Creates an iterator over the tables in the data set. If $reverse is
     * true a reverse iterator will be returned.
     *
     * @param bool $reverse
     *
     * @return ITableIterator
     */
    protected function createIterator($reverse = false)
    {
        return new DefaultTableIterator(
            $this->tables,
            $reverse
        );
    }

    /**
     * Creates a unique list of columns from all the rows in a table.
     * If the table is defined another time in the Yaml, and if the Yaml
     * parser could return the multiple occerrences, then this would be
     * insufficient unless we grouped all the occurences of the table
     * into onwe row set.  sfYaml, however, does not provide multiple tables
     * with the same name, it only supplies the last table.
     *
     * @params all the rows in a table.
     *
     * @param mixed $rows
     */
    private function getColumns($rows)
    {
        $columns = [];

        foreach ($rows as $row) {
            $columns = \array_merge($columns, \array_keys($row));
        }

        return \array_values(\array_unique($columns));
    }
}