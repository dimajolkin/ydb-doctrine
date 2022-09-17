<?php

namespace Dimajolkin\YdbDoctrine;

use Dimajolkin\YdbDoctrine\Platform\Keywords;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\TableDiff;
use YandexCloud\Ydb\Ydb;

class YdbPlatform extends AbstractPlatform
{

    protected function getVarcharTypeDeclarationSQLSnippet($length, $fixed/*, $lengthOmitted = false*/)
    {
        return 'String';
    }

    public function getAlterTableSQL(TableDiff $diff)
    {
        return;
    }

    public function supportsSequences()
    {
        return true;
    }


    /**
     * Нет DEFAULT
     */
    public function getDefaultValueDeclarationSQL($column)
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getDateTimeTypeDeclarationSQL(array $column)
    {
        return 'Datetime';
    }

    /**
     * {@inheritDoc}
     */
    public function getDateTimeTzTypeDeclarationSQL(array $column)
    {
        return 'TzDateTime';
    }

    public function getBooleanTypeDeclarationSQL(array $column)
    {
        return 'Bool';
    }

    public function getIntegerTypeDeclarationSQL(array $column)
    {
        return 'Int32';
    }

    public function getBigIntTypeDeclarationSQL(array $column)
    {
        return 'Int64';
    }

    public function getSmallIntTypeDeclarationSQL(array $column)
    {
        return 'Int16';
    }

    protected function _getCommonIntegerTypeDeclarationSQL(array $column)
    {
        return '';
    }

    protected function initializeDoctrineTypeMappings()
    {
        $this->doctrineTypeMapping = [
            'bigint'           => 'bigint',
            'bigserial'        => 'bigint',
            'bool'             => 'boolean',
            'boolean'          => 'boolean',
            'bpchar'           => 'string',
            'bytea'            => 'blob',
            'char'             => 'string',
            'date'             => 'date',
            'datetime'         => 'datetime',
            'decimal'          => 'decimal',
            'double'           => 'float',
            'double precision' => 'float',
            'float'            => 'float',
            'float4'           => 'float',
            'float8'           => 'float',
            'inet'             => 'string',
            'int'              => 'integer',
            'int2'             => 'smallint',
            'int4'             => 'integer',
            'int8'             => 'bigint',
            'integer'          => 'integer',
            'interval'         => 'string',
            'json'             => 'json',
            'jsonb'            => 'json',
            'money'            => 'decimal',
            'numeric'          => 'decimal',
            'serial'           => 'integer',
            'serial4'          => 'integer',
            'serial8'          => 'bigint',
            'real'             => 'float',
            'smallint'         => 'smallint',
            'text'             => 'text',
            'time'             => 'time',
            'timestamp'        => 'datetime',
            'timestamptz'      => 'datetimetz',
            'timetz'           => 'time',
            'tsvector'         => 'text',
            'uuid'             => 'guid',
            'varchar'          => 'string',
            'year'             => 'date',
            '_varchar'         => 'string',
        ];
    }

    public function getClobTypeDeclarationSQL(array $column)
    {
        return 'String';
    }

    public function getBlobTypeDeclarationSQL(array $column)
    {
        return 'String';
    }

    public function getName()
    {
        return 'ydb';
    }

    public function getCurrentDatabaseExpression(): string
    {
        return 'CurrentUtcDate()';
    }

    protected function getReservedKeywordsClass(): string
    {
        return Keywords::class;
    }
}
