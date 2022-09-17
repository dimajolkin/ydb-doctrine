<?php

namespace Dimajolkin\YdbDoctrine;

use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Driver\Result;
use YandexCloud\Ydb\QueryResult;

class YdbResult implements Result
{
    public function __construct(
        private QueryResult $queryResult
    ) {}

    public function fetchNumeric()
    {
        $list = [];
        foreach ($this->queryResult->rows() as $row) {
            $list[] = array_values($row)[0];
        }

        return $list;
    }

    public function fetchAssociative()
    {
        return $this->queryResult->rows()[0] ?? false;
    }

    public function fetchOne()
    {
        return $this->queryResult->value();
    }

    public function fetchAllNumeric(): array
    {
        $list = [];
        foreach ($this->queryResult->rows() as $row) {
            $list[] = array_values($row);
        }

        return $list;
    }

    public function fetchAllAssociative(): array
    {
        return $this->queryResult->rows();
    }

    public function fetchFirstColumn(): array
    {
        foreach ($this->queryResult->rows() as $row) {
            return [
                array_values($row)[0]
            ];
        }

        return [];
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

    }
}
