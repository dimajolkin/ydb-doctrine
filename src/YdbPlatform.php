<?php

namespace Dimajolkin\YdbDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class YdbPlatform extends AbstractPlatform
{
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
}
