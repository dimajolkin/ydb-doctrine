<?php

namespace Dimajolkin\YdbDoctrine\Yql\Parser;

class OrderField
{
    public function __construct(
        public Field $field,
        public string $order,
    ) {}
}
