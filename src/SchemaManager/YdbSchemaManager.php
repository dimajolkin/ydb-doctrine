<?php

namespace Dimajolkin\YdbDoctrine\SchemaManager;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
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
