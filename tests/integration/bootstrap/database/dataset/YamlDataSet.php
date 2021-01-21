<?php

namespace Tests\Integration\Bootstrap\Database\DataSet;

class YamlDataSet extends AbstractDataSet
{
    protected $tables = [];

    protected $parser;

    public function __construct($yamlFile, $parser = null)
    {
        if ($parser == null) {
            $parser = new SymfonyYamlParser();
        }
        $this->parser = $parser;
        $this->addYamlFile($yamlFile);
    }

    public function addYamlFile($yamlFile)
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

    protected function createIterator($reverse = false)
    {
        return new DefaultTableIterator(
            $this->tables,
            $reverse
        );
    }

    private function getColumns($rows)
    {
        $columns = [];

        foreach ($rows as $row) {
            $columns = \array_merge($columns, \array_keys($row));
        }

        return \array_values(\array_unique($columns));
    }
}
