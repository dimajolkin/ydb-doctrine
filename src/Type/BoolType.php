<?php

namespace Dimajolkin\YdbDoctrine\Type;

use Dimajolkin\YdbDoctrine\YdbTypes;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\BooleanType;

class BoolType extends BooleanType
{
    public function getName(): string
    {
        return YdbTypes::BOOL;
    }

    public function getBindingType(): int
    {
        return ParameterType::BOOLEAN;
    }
}
