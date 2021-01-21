<?php

namespace Tests\Integration\Bootstrap\Database\Metadata;

use PDO;
use ReflectionClass;

abstract class AbstractMetadata
{
    protected static $metaDataClassMap = [
        'mysql' => MySQL::class
    ];

    protected $pdo;

    protected $schema;

    protected $schemaObjectQuoteChar = '"';

    protected $truncateCommand = 'TRUNCATE';

    public static function createMetaData(PDO $pdo, $schema = '')
    {
        $driverName = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        if (isset(self::$metaDataClassMap[$driverName])) {
            $className = self::$metaDataClassMap[$driverName];

            if ($className instanceof ReflectionClass) {
                return $className->newInstance($pdo, $schema);
            }

            return self::registerClassWithDriver($className, $driverName)->newInstance($pdo, $schema);
        }

        throw new \Exception("Could not find a meta data driver for {$driverName} pdo driver.");
    }

    public static function registerClassWithDriver($className, $pdoDriver)
    {
        if (!\class_exists($className)) {
            throw new \Exception("Specified class for {$pdoDriver} driver ({$className}) does not exist.");
        }

        $reflection = new ReflectionClass($className);

        if ($reflection->isSubclassOf(self::class)) {
            return self::$metaDataClassMap[$pdoDriver] = $reflection;
        }

        throw new \Exception("Specified class for {$pdoDriver} driver ({$className}) does not extend PHPUnit_Extensions_Database_DB_MetaData.");
    }

    final public function __construct(PDO $pdo, $schema = '')
    {
        $this->pdo    = $pdo;
        $this->schema = $schema;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function quoteSchemaObject($object)
    {
        $parts       = \explode('.', $object);
        $quotedParts = [];

        foreach ($parts as $part) {
            $quotedParts[] = $this->schemaObjectQuoteChar .
                \str_replace($this->schemaObjectQuoteChar, $this->schemaObjectQuoteChar . $this->schemaObjectQuoteChar, $part) .
                $this->schemaObjectQuoteChar;
        }

        return \implode('.', $quotedParts);
    }

    public function splitTableName($fullTableName)
    {
        if (($dot = \strpos($fullTableName, '.')) !== false) {
            return [
                'schema' => \substr($fullTableName, 0, $dot),
                'table'  => \substr($fullTableName, $dot + 1)
            ];
        }

        return [
            'schema' => null,
            'table'  => $fullTableName
        ];
    }

    public function getTruncateCommand()
    {
        return $this->truncateCommand;
    }

    public function allowsCascading()
    {
        return false;
    }

    public function disablePrimaryKeys($tableName): void
    {
        return;
    }

    public function enablePrimaryKeys($tableName): void
    {
        return;
    }
}