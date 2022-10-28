<?php

namespace Dimajolkin\YdbDoctrine\Type;

use Dimajolkin\YdbDoctrine\ParameterType;

class DecimalType extends \Doctrine\DBAL\Types\DecimalType
{
    public function getBindingType(): int
    {
        return ParameterType::DECIMAL;
    }
}
