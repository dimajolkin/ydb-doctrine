<?php

namespace Dimajolkin\YdbDoctrine;

use Dimajolkin\YdbDoctrine\Platform\Keywords;
use Dimajolkin\YdbDoctrine\Type\DateTimeType;
use Dimajolkin\YdbDoctrine\Type\DateTimeTzType;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints\DateTime;
use YandexCloud\Ydb\Ydb;

class YdbPlatform extends AbstractPlatform
{

    protected function getVarcharTypeDeclarationSQLSnippet($length, $fixed/*, $lengthOmitted = false*/)
    {
        return 'Utf8';
    }

    protected function getBinaryTypeDeclarationSQLSnippet($length, $fixed)
    {
        return 'String';
    }

    public function getJsonTypeDeclarationSQL(array $column)
    {
        return 'Json';
    }

    public function getAlterTableSQL(TableDiff $diff)
    {
        return;
    }

    public function supportsSequences()
    {
        // false
        return true;
    }

    public function convertBooleans($item)
    {
        if (is_array($item)) {
            foreach ($item as $k => $value) {
                if (! is_bool($value)) {
                    continue;
                }

                $item[$k] = $value ? 'true' : 'false';
            }
        } elseif (is_bool($item)) {
            $item = $item ? 'true' : 'false';
        }

        return $item;
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

    public function getDateTypeDeclarationSQL(array $column)
    {
        return 'Date';
    }

    /**
     * {@inheritDoc}
     */
    public function getDateTimeTzTypeDeclarationSQL(array $column)
    {
        return 'Datetime';
    }

    public function getDateTimeFormatString()
    {
        return 'Y-m-d H:i:s';
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
        $this->doctrineTypeMapping = [];
    }

    public function getClobTypeDeclarationSQL(array $column)
    {
        return 'String';
    }

    public function getBlobTypeDeclarationSQL(array $column)
    {
        return 'String';
    }

    public function getFloatDeclarationSQL(array $column)
    {
        return 'Float';
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
