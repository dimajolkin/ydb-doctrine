<?php

namespace Dimajolkin\YdbDoctrine;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;

final class YdbTypes
{
    public const SMALL_INT = self::INT16;
    public const BIG_INT = self::INT64;
    public const INTEGER = self::INT32;
    public const BINARY = self::STRING;
    public const TEXT = self::UTF8;

    public const BOOL = 'bool';
    public const INT8 = 'int8';
    public const INT16 = 'int16';
    public const INT32 = 'int32';
    public const INT64 = 'int64';
    public const UINT8 = 'uint8';
    public const UINT32 = 'uint32';
    public const UINT64 = 'uint64';
    public const FLOAT = 'float';
    public const DOUBLE = 'double';
    public const DECIMAL = 'decimal';
    public const STRING = 'string';
    public const UTF8 = 'utf8';
    public const JSON = 'json';
    public const JSON_DOCUMENT = 'jsonDocument';
    public const YSON = 'yson';
    public const UUID = 'uuid';
    public const DATE = 'date';
    public const DATETIME = 'datetime';
    public const TIMESTAMP = 'timestamp';
    public const INTERVAL = 'interval';

    private const MAP_TO_DBAL_TYPES = [
        self::STRING => Types::STRING,
        self::UTF8 => Types::STRING,
        self::JSON => Types::JSON,
        self::DATETIME => Types::DATETIME_MUTABLE,
        self::INT32 => Types::INTEGER,
        self::BOOL => Types::BOOLEAN,
        self::DATE => Types::DATETIME_MUTABLE,
        self::FLOAT => Types::FLOAT,
    ];

    public static function toDbalType(string $name): Type
    {
        $type = self::MAP_TO_DBAL_TYPES[strtolower($name)] ?? throw new \Exception("$name not mapped");

        return Type::getType($type);
    }
}
