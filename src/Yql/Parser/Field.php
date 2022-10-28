<?php

namespace Dimajolkin\YdbDoctrine\Yql\Parser;

class Field
{
    public function __construct(
        public string $tableName,
        public string $columnName,
        public ?string $alias
    ) {

    }

    public function getName(): string
    {
        return $this->tableName . '.' . $this->columnName;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function getKey(): string
    {
        return $this->getAlias() ?: $this->getName();
    }
}
