<?php

namespace Dimajolkin\YdbDoctrine\Driver;

use Dimajolkin\YdbDoctrine\Parser\YdbUriParser;
use Dimajolkin\YdbDoctrine\YdbStatement;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;
use Psr\Log\LoggerInterface;
use YdbPlatform\Ydb\Table;
use YdbPlatform\Ydb\Ydb;

final class YdbConnection implements Connection
{
    private Table $table;

    public function __construct(
        private Ydb $ydb
    ) {
        $this->table = $this->ydb->table();
        $this->table->session()->keepAlive();
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
        return new YdbStatement($this, $sql, $this->table);
    }

    public function query(string $sql): Result
    {
        return $this->prepare($sql)->execute();
    }

    public function quote($value, $type = ParameterType::STRING)
    {
        if (ParameterType::STRING === $type) {
            $value = \addslashes(\addslashes($value));

            return "'$value'";
        }

        if (ParameterType::BOOLEAN === $type) {
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
        try {
            $this->table->session()?->beginTransaction();

            return true;
        } catch (\Exception) {
            return false;
        }
    }

    public function commit(): bool
    {
        $this->table->session()?->commit();

        return true;
    }

    public function rollBack(): bool
    {
        try {
            $this->table->session()?->rollBack();
        } catch (\Throwable) {
        }

        return true;
    }
}
