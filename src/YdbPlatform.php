<?php

namespace Dimajolkin\YdbDoctrine;

use Dimajolkin\YdbDoctrine\Platform\Keywords;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\TableDiff;

class YdbPlatform extends AbstractPlatform
{
    protected function initializeCommentedDoctrineTypes(): void
    {
    }

    public function getDecimalTypeDeclarationSQL(array $column): string
    {
        return 'decimal';
    }

    public function hasNativeGuidType(): bool
    {
        return true;
    }

    public function getGuidTypeDeclarationSQL(array $column): string
    {
        return YdbTypes::UUID;
    }

    protected function getVarcharTypeDeclarationSQLSnippet($length, $fixed/* , $lengthOmitted = false */): string
    {
        return YdbTypes::TEXT;
    }

    protected function getBinaryTypeDeclarationSQLSnippet($length, $fixed): string
    {
        return YdbTypes::BINARY;
    }

    public function getJsonTypeDeclarationSQL(array $column): string
    {
        return YdbTypes::JSON;
    }

    public function getAlterTableSQL(TableDiff $diff): string
    {
        return '';
    }

    public function supportsSequences(): bool
    {
        // false
        return true;
    }

    public function convertBooleans($item): mixed
    {
        if (is_array($item)) {
            foreach ($item as $k => $value) {
                if (!is_bool($value)) {
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
     * Нет DEFAULT.
     */
    public function getDefaultValueDeclarationSQL($column): string
    {
        return '';
    }

    public function getDateTimeTypeDeclarationSQL(array $column): string
    {
        return YdbTypes::DATETIME;
    }

    public function getDateTypeDeclarationSQL(array $column): string
    {
        return YdbTypes::DATE;
    }

    public function getDateTimeTzTypeDeclarationSQL(array $column): string
    {
        return YdbTypes::DATETIME;
    }

    public function getDateTimeFormatString(): string
    {
        return 'Y-m-d H:i:s';
    }

    public function getBooleanTypeDeclarationSQL(array $column): string
    {
        return YdbTypes::BOOL;
    }

    public function getIntegerTypeDeclarationSQL(array $column): string
    {
        return YdbTypes::INTEGER;
    }

    public function getBigIntTypeDeclarationSQL(array $column): string
    {
        return YdbTypes::BIG_INT;
    }

    public function getSmallIntTypeDeclarationSQL(array $column): string
    {
        return YdbTypes::STRING;
    }

    protected function _getCommonIntegerTypeDeclarationSQL(array $column): string
    {
        return '';
    }

    protected function initializeDoctrineTypeMappings(): void
    {
        $this->doctrineTypeMapping = [];
    }

    public function getClobTypeDeclarationSQL(array $column): string
    {
        return YdbTypes::STRING;
    }

    public function getBlobTypeDeclarationSQL(array $column): string
    {
        return YdbTypes::STRING;
    }

    public function getFloatDeclarationSQL(array $column): string
    {
        return YdbTypes::FLOAT;
    }

    public function getName(): string
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
