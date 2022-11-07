<?php

namespace Dimajolkin\YdbDoctrine\Type;

use Dimajolkin\YdbDoctrine\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class JsonType extends \Doctrine\DBAL\Types\JsonType
{
    public function getBindingType(): int
    {
        return ParameterType::JSON;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (!$value) {
            return null;
        }

        return (array) $value;
    }
}
