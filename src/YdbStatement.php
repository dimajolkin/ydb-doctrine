<?php

namespace Dimajolkin\YdbDoctrine;

use Dimajolkin\YdbDoctrine\Driver\YdbConnection;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;
use Ydb\Type;
use Ydb\TypedValue;
use YdbPlatform\Ydb\Table;
use YdbPlatform\Ydb\Traits\TypeValueHelpersTrait;

class YdbStatement implements Statement
{
    use TypeValueHelpersTrait;

    /** @var array<int, array<mixed, string> */
    private array $bindValues = [];
    private array $parameters = [];

    private int $reconnect = 0;

    public function __construct(
        private YdbConnection $connection,
        private string $sql,
        private Table $table,
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
        $index = 1;
        $declareSql = [];
        foreach ($this->bindValues as $param => [$value, $type]) {
            if (null === $value) {
                $rawSql = preg_replace('/\?/', 'NULL', $rawSql, 1);
            } else {
                $name = '$col'.$index++;
                $type = $this->makeYdbType($value, $type);
                $typeName = Type\PrimitiveTypeId::name($type->getType()->getTypeId());
                $rawSql = preg_replace('/\?/', $name, $rawSql, 1);
                $this->parameters[$name] = $type;
                $declareSql[] = sprintf("DECLARE $name AS %s;\n", $typeName);
            }
        }

        return implode($declareSql).''.$rawSql;
    }

    private function makeYdbType($value, $type): TypedValue
    {
        if (\Dimajolkin\YdbDoctrine\ParameterType::STRING === $type) {
            return $this->typeValue((string) $value, 'UTF8')->toTypedValue();
        } elseif (\Dimajolkin\YdbDoctrine\ParameterType::BINARY === $type) {
            return $this->typeValue((string) $value, 'STRING')->toTypedValue();
        } elseif (\Dimajolkin\YdbDoctrine\ParameterType::INTEGER === $type) {
            return $this->typeValue((int) $value, 'INT32')->toTypedValue();
        } elseif (\Dimajolkin\YdbDoctrine\ParameterType::BOOLEAN === $type) {
            if ('true' === $value) {
                $value = true;
            } elseif ('false' === $value) {
                $value = false;
            } else {
                throw new \Exception("Undefined bool value equals $value");
            }

            return $this->typeValue($value, 'BOOL')->toTypedValue();
        } elseif (\Dimajolkin\YdbDoctrine\ParameterType::DATETIME === $type) {
            if ($value instanceof \DateTimeImmutable) {
                $value = \DateTime::createFromImmutable($value);
            }

            return $this->typeValue($value, 'DATETIME')->toTypedValue();
        } elseif (\Dimajolkin\YdbDoctrine\ParameterType::JSON === $type) {
            return $this->typeValue($value, 'JSON')->toTypedValue();
        } elseif (\Dimajolkin\YdbDoctrine\ParameterType::FLOAT === $type) {
            return $this->typeValue($value, 'FLOAT')->toTypedValue();
        } elseif (\Dimajolkin\YdbDoctrine\ParameterType::DECIMAL === $type) {
            return $this->typeValue($value, 'FLOAT')->toTypedValue();
        } elseif (\Dimajolkin\YdbDoctrine\ParameterType::TIMESTAMP === $type) {
            return $this->typeValue($value, 'TIMESTAMP')->toTypedValue();
        } elseif (\Dimajolkin\YdbDoctrine\ParameterType::UINT32 === $type) {
            return $this->typeValue($value, 'UINT32')->toTypedValue();
        } elseif (\Dimajolkin\YdbDoctrine\ParameterType::UINT64 === $type) {
            return $this->typeValue($value, 'UINT64')->toTypedValue();
        }
        throw new \Exception("$type, $value not support");
    }

    public function execute($params = null): Result
    {
        $sql = $this->getRawSql();
        try {
            if (str_starts_with($sql, 'CREATE')) {
                $status = $this->table->schemeQuery($sql);

                return new YdbSchemaResult($status);
            } else {
                $res = $this->table->prepare($sql)->execute($this->parameters);

                return new YdbResult($res);
            }
        } catch (\Exception|\Throwable $ex) {
            // close soket
            //            if (str_contains($ex->getMessage(), 'Socket closed')) {
            //                $this->reconnect++;
            //                if ($this->reconnect < 3) {
            //                    return $this->execute($params);
            //                }
            //            }

            throw new \Exception($sql."\n".' Details: '.$ex->getMessage(), previous: $ex);
        }
    }
}
