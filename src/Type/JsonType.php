<?php

namespace Dimajolkin\YdbDoctrine\Type;

use Dimajolkin\YdbDoctrine\ParameterType;

class JsonType extends \Doctrine\DBAL\Types\JsonType
{
    public function getBindingType(): int
    {
        return ParameterType::JSON;
    }
}