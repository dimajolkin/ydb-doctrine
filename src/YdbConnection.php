<?php

namespace Dimajolkin\YdbDoctrine;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;
use YandexCloud\Ydb\Ydb;

class YdbConnection implements Connection
{
    public function __construct(
        private Ydb $ydb
    ) { }

    public function prepare(string $sql): Statement
    {
        // TODO: Implement prepare() method.
    }

    public function query(string $sql): Result
    {
        $table = $this->ydb->table();
        $sesion = $table->session();
        $res = $sesion->query($sql);

        return new YdbResult($res);
    }

    public function quote($value, $type = ParameterType::STRING)
    {
        // TODO: Implement quote() method.
    }

    public function exec(string $sql): int
    {
        // TODO: Implement exec() method.
    }

    public function lastInsertId($name = null)
    {
        // TODO: Implement lastInsertId() method.
    }

    public function beginTransaction()
    {
        // TODO: Implement beginTransaction() method.
    }

    public function commit()
    {
        // TODO: Implement commit() method.
    }

    public function rollBack()
    {
        // TODO: Implement rollBack() method.
    }
}
