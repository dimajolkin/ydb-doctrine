<?php

namespace Dimajolkin\YdbDoctrine;

use Doctrine\DBAL\Cache\ArrayResult;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Driver\Result;
use YandexCloud\Ydb\QueryResult;

class YdbResult implements Result
{
    private ArrayResult $result;

    public function __construct(
        private QueryResult $queryResult
    ) {
        $this->result = new ArrayResult($this->queryResult->rows());
    }

    public function fetchNumeric()
    {
        return $this->result->fetchNumeric();
    }

    public function fetchAssociative(): array|false
    {
        return $this->result->fetchAssociative();
    }

    public function fetchOne(): mixed
    {
        return $this->queryResult->value();
    }

    public function fetchAllNumeric(): array
    {
        return $this->result->fetchAllNumeric();
    }

    public function fetchAllAssociative(): array
    {
        return $this->result->fetchAllAssociative();
    }

    public function fetchFirstColumn(): array
    {
        return $this->result->fetchFirstColumn();
    }

    public function rowCount(): int
    {
        return $this->queryResult->rowCount();
    }

    public function columnCount(): int
    {
        return $this->queryResult->columnCount();
    }

    public function free(): void
    {
        $this->result->free();;
    }
}
