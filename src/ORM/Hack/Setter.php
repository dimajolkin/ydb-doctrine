<?php

namespace Dimajolkin\YdbDoctrine\ORM\Hack;

class Setter
{
    public function __construct(
        private object $object,
        private string $parentClassName
    ) {
    }

    private function execute(callable $func): mixed
    {
        return \Closure::bind($func, $this)->bindTo($this->object, $this->parentClassName)();
    }

    public function setValue(string $property, mixed $value): void
    {
        $this->execute(function () use ($property, $value) {
            /* @var \stdClass $this */
            $this->{$property} = $value;
        });
    }

    public function getValue(string $property): mixed
    {
        return $this->execute(function () use ($property) {
            /* @var \stdClass $this */
            return $this->{$property};
        });
    }
}
