<?php

namespace Dimajolkin\YdbDoctrine;

use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\Types;
use YandexCloud\Ydb\Session;
use Ydb\Type;
use function PHPUnit\Framework\throwException;

class YdbStatement implements Statement
{

    /** @var array<int, array<mixed, string> */
    private array $bindValues = [];

    public function __construct(
        private YdbConnection $connection,
        private string $sql,
        private Session $session,
    ) {

    }

    public function bindValue($param, $value, $type = ParameterType::STRING): bool
    {
        $this->bindValues[$param] = [$value, $type];

        return true;
    }

    public function bindParam($param, &$variable, $type = ParameterType::STRING, $length = null): bool
    {
        throw new \Exception('YdbStatement::bindParam don\'t imp');
    }

    public function getRawSql(): string
    {
        $rawSql = $this->sql;
        foreach ($this->bindValues as $param => [$value, $type]) {
            $rawSql = preg_replace('/\?/', $this->connection->quote($value, $type), $rawSql, 1);
        }

        return $rawSql;
    }

    public function execute($params = null): Result
    {
        $sql = $this->getRawSql();

        return $this->session->transaction(function () use ($sql) {
            if (str_starts_with($sql, 'CREATE')) {
                $status = $this->session->schemeQuery($sql);
                return new YdbSchemaResult($status);
            } else {
                $res = $this->session->query($sql);
                return new YdbResult($res);
            }
        });
    }
}
