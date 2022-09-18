<?php

namespace Dimajolkin\YdbDoctrine;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Driver\ServerInfoAwareConnection;
use Doctrine\DBAL\ParameterType;
use YandexCloud\Ydb\Session;
use YandexCloud\Ydb\Table;
use YandexCloud\Ydb\Ydb;

class YdbConnection implements Connection, ServerInfoAwareConnection
{
    private ?Session $session = null;

    public function __construct(
        private Ydb $ydb
    ) {
    }

    public function getServerVersion()
    {
        return '1.0';
    }

    public function prepare(string $sql): Statement
    {
        $this->session = $this->ydb->table()->session();
        return new YdbStatement($this, $sql, $this->session);
    }

    public function query(string $sql): Result
    {
        return $this->prepare($sql)->execute();
    }

    public function quote($value, $type = ParameterType::STRING)
    {
        if ($type === ParameterType::STRING) {
            $value = addslashes(addslashes($value));
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
        return true;
    }

    public function commit()
    {
        $this->session?->commit();
        return true;
    }

    public function rollBack()
    {
        try {
            $this->session?->rollBack();
        } catch (\Throwable) {}
        return true;
    }
}
