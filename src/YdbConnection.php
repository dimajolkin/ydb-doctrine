<?php

namespace Dimajolkin\YdbDoctrine;

use Dimajolkin\YdbDoctrine\Parser\YdbUriParser;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\ServerInfoAwareConnection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;
use Psr\Log\LoggerInterface;
use YandexCloud\Ydb\Session;
use YandexCloud\Ydb\Ydb;

class YdbConnection implements Connection, ServerInfoAwareConnection
{
    private Session $session;

    public function __construct(
        private Ydb $ydb
    ) {
        $this->session = $this->ydb->table()->session() ?? throw new \Exception();
    }

    public static function makeConnectionByUrl(string $dbUri, LoggerInterface $logger = null): YdbConnection
    {
        $config = (new YdbUriParser())->parse($dbUri);
        $ydb = new Ydb($config, $logger);

        return new YdbConnection($ydb);
    }

    public function getYdb(): Ydb
    {
        return $this->ydb;
    }

    public function getServerVersion(): string
    {
        return Ydb::VERSION;
    }

    public function prepare(string $sql): Statement
    {
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

        if ($type === ParameterType::BOOLEAN) {
            return $type ? 'true' : 'false';
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

    public function beginTransaction(): bool
    {
        $this->session->beginTransaction();
        return true;
    }

    public function commit(): bool
    {
        $this->session->commit();
        return true;
    }

    public function rollBack(): bool
    {
        try {
            $this->session->rollBack();
        } catch (\Throwable) {}
        return true;
    }
}
