<?php

namespace Dimajolkin\YdbDoctrine\Type;

use Dimajolkin\YdbDoctrine\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;

class DateTimeType extends Type
{
    public function getName(): string
    {
        return Types::DATETIME_MUTABLE;
    }

    public function getBindingType(): int
    {
        return ParameterType::DATETIME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTimeTypeDeclarationSQL($column);
    }

    public function canRequireSQLConversion(): bool
    {
        return true;
    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform): string
    {
        return "CAST($sqlExpr as Datetime)";
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'DateTime']);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (null === $value || $value instanceof \DateTimeInterface) {
            return $value;
        }

        $val = \DateTime::createFromFormat($platform->getDateTimeFormatString(), $value);

        if (false === $val) {
            $val = date_create($value);
        }

        if (false === $val) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeFormatString());
        }

        return $val;
    }
}
