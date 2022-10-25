<?php

namespace Dimajolkin\YdbDoctrine\Type;

use Dimajolkin\YdbDoctrine\ParameterType;

class FloatType extends \Doctrine\DBAL\Types\FloatType
{
    public function getBindingType(): int
    {
        return ParameterType::FLOAT;
    }
}
