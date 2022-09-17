<?php

namespace Dimajolkin\YdbDoctrine;

use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;
use YandexCloud\Ydb\Session;
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

    public function bindValue($param, $value, $type = ParameterType::STRING)
    {
        $this->bindValues[$param] = [$value, $type];
    }

    public function bindParam($param, &$variable, $type = ParameterType::STRING, $length = null)
    {
        throw new \Exception('YdbStatement::bindParam don\'t imp');
    }

    public function getRawSql(): string
    {
        $rawSql = $this->sql;
        foreach ($this->bindValues as $param => [$value, $type]) {
            $rawSql = preg_replace("/\?/", $this->connection->quote($value, $type), $rawSql, 1);
        }

        return $rawSql;
    }

    public function execute($params = null): Result
    {
        $sql = $this->getRawSql();
        try {
            $this->session->beginTransaction();
            if (str_starts_with($sql, 'CREATE')) {
                $status = $this->session->schemeQuery($sql);
                return new YdbSchemaResult($status);
            } else {
                $res = $this->session->query($sql);
            }
            $this->session->commit();
        } catch (\Throwable $exception) {

            try {
                $this->session->rollBack();
            } catch (\Throwable) {}

            throw $exception;
        }

        return new YdbResult($res);
    }
}
