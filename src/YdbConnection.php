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
    private Table $table;

    public function __construct(
        private Ydb $ydb
    ) {
        $this->table = $this->ydb->table();
    }

    public function prepare(string $sql): Statement
    {
        return new YdbStatement($this, $sql);
    }

    public function query(string $sql): Result
    {
        try {
            $res = $this->table->query($sql);
            $this->table->session()->commit();
        } catch (\Throwable $exception) {
            try {
                $this->table->session()->rollBack();
            } catch (\Exception $exception) {}
            throw $exception;
        }

        return new YdbResult($res);
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
