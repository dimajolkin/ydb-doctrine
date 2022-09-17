<?php

namespace Dimajolkin\YdbDoctrine;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;
use YandexCloud\Ydb\Session;
use YandexCloud\Ydb\Ydb;

class YdbConnection implements Connection
{
    private ?Session $session = null;

    public function __construct(
        private Ydb $ydb
    ) { }

    public function prepare(string $sql): Statement
    {
        throw new \Exception();
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
        return $value;
    }

    public function exec(string $sql): int
    {
        $res = $this->query($sql);

        return $res->rowCount();
    }

    public function lastInsertId($name = null)
    {
        throw new \Exception();
    }

    public function beginTransaction()
    {
        $this->session?->beginTransaction();
    }

    public function commit()
    {
        $this->session?->commit();
    }

    public function rollBack()
    {
        $this->session?->rollBack();
    }
}
