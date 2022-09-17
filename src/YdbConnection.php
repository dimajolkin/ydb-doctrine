<?php

namespace Dimajolkin\YdbDoctrine;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;
use YandexCloud\Ydb\Session;
use YandexCloud\Ydb\Table;
use YandexCloud\Ydb\Ydb;

class YdbConnection implements Connection
{
    private ?Session $session = null;

    public function __construct(
        private Ydb $ydb
    ) {
    }

    public function prepare(string $sql): Statement
    {
        return new YdbStatement($this, $sql, $this->ydb->table()->session());
    }

    public function query(string $sql): Result
    {
        return $this->prepare($sql)->execute();
    }

    public function quote($value, $type = ParameterType::STRING)
    {
        if ($type === ParameterType::STRING) {
            $value = pg_escape_string($value);

            return "'$value'";
        }

        return $value;
    }

    public function exec(string $sql): int
    {
        return $this->query($sql)->rowCount();
    }

    public function lastInsertId($name = null)
    {
        throw new \Exception();
    }

    public function beginTransaction()
    {
//        $this->session?->beginTransaction();
    }

    public function commit()
    {
//        $this->session?->commit();
    }

    public function rollBack()
    {
//        $this->session?->rollBack();
    }
}
