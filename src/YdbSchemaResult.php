<?php

namespace Dimajolkin\YdbDoctrine;

use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Driver\Result;
use YandexCloud\Ydb\QueryResult;

class YdbSchemaResult implements Result
{
    public function __construct(
        private bool $status,
    ) {}

    public function fetchNumeric()
    {
        return 1;
    }

    public function fetchAssociative()
    {
        return false;
    }

    public function fetchOne()
    {
        return false;
    }

    public function fetchAllNumeric(): array
    {
        return [];
    }

    public function fetchAllAssociative(): array
    {
        return [];
    }

    public function fetchFirstColumn(): array
    {
        return [];
    }

    public function rowCount(): int
    {
        return 1;
    }

    public function columnCount(): int
    {
        return 1;
    }

    public function free(): void
    {

    }
}
