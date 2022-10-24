<?php

namespace Dimajolkin\YdbDoctrine\SchemaManager;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use YandexCloud\Ydb\Ydb;

class YdbSchemaManager extends AbstractSchemaManager
{
    private Ydb $ydb;

    public function __construct(Connection $connection, AbstractPlatform $platform, Ydb $ydb)
    {
        $this->ydb = $ydb;
        parent::__construct($connection, $platform);
    }

    protected function _getPortableTableColumnDefinition($tableColumn)
    {
        // TODO: Implement _getPortableTableColumnDefinition() method.
    }

    private function bindType(string $type): Type
    {
        return match ($type) {
            'STRING' => Type::getType(Types::STRING),
            'UTF8' => Type::getType(Types::STRING),
            'JSON' => Type::getType(Types::JSON),
            'DATETIME' => Type::getType(Types::DATETIME_MUTABLE),
            'INT32' => Type::getType(Types::INTEGER),
            'BOOL' => Type::getType(Types::BOOLEAN),
            'DATE' => Type::getType(Types::DATETIME_MUTABLE),
            'FLOAT' => Type::getType(Types::FLOAT),
            default => throw new \Exception("$type not support"),
        };
    }

    public function listTableColumns($table, $database = null): array
    {
        $list = [];
        $data = $this->ydb->table()->session()->describeTable($table);
        foreach ($data['columns'] as $column) {
            $notnull = true;
            $type = $column['type']['typeId'] ?? null;
            if (!$type) {
                $type = $column['type']['optionalType']['item']['typeId'] ?? throw new \Exception();
                $notnull = false;
            }

            $list[] = new Column($column['name'], $this->bindType($type), ['notnull' => $notnull]);
        }

        return $list;
    }

    public function listTableForeignKeys($table, $database = null): array
    {
        return [];
    }

    public function listTableIndexes($table): array
    {
        $data = $this->ydb->table()->session()->describeTable($table);
        $indexes = [];
        $columns = $data['primaryKey'];
        $indexes[] = new Index('primary', $columns, true, true, [], []);
        return $indexes;
    }

    public function alterTable(TableDiff $tableDiff): void
    {
        // change
    }

    public function listTableNames(): array
    {
        $tableNames = [];
        foreach ($this->ydb->scheme()->listDirectory() as $table) {
            if ($table['type'] === 'TABLE') {
                $tableNames[] = $table['name'];
            }
        }

        return $this->filterAssetNames($tableNames);
    }
}
