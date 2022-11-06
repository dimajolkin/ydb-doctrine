<?php

namespace Dimajolkin\YdbDoctrine;

class ParameterType
{
    const DATETIME = 1000;
    const JSON = 1001;
    const FLOAT = 1002;
    const DECIMAL = 1003;
    const TIMESTAMP = 1004;

    const BINARY = \Doctrine\DBAL\ParameterType::BINARY;
    const INTEGER = \Doctrine\DBAL\ParameterType::INTEGER;
    const STRING = \Doctrine\DBAL\ParameterType::STRING;
    const BOOLEAN = \Doctrine\DBAL\ParameterType::BOOLEAN;

    const UINT32 = 1005;
    const UINT64 = 1006;
}
