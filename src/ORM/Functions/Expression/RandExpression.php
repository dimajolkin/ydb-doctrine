<?php

namespace Dimajolkin\YdbDoctrine\ORM\Functions\Expression;

use Dimajolkin\YdbDoctrine\ORM\Query\YdbWalker;
use Doctrine\ORM\Query\AST\Node;

class RandExpression extends Node
{
    public function __construct(
        private string $tableAlias,
        private string $columnName
    ) {
    }

    public function getTableAlias(): string
    {
        return $this->tableAlias;
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function dispatch($walker): string
    {
        if (!$walker instanceof YdbWalker) {
            throw new \Exception();
        }

        return $walker->walkRandExpression($this);
    }
}
