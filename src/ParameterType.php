<?php

namespace Dimajolkin\YdbDoctrine;

class ParameterType
{
    public const DATETIME = 1000;
    public const JSON = 1001;
    public const FLOAT = 1002;
    public const DECIMAL = 1003;
    public const TIMESTAMP = 1004;

    public const BINARY = \Doctrine\DBAL\ParameterType::BINARY;
    public const INTEGER = \Doctrine\DBAL\ParameterType::INTEGER;
    public const STRING = \Doctrine\DBAL\ParameterType::STRING;
    public const BOOLEAN = \Doctrine\DBAL\ParameterType::BOOLEAN;

    public const UINT32 = 1005;
    public const UINT64 = 1006;
}
