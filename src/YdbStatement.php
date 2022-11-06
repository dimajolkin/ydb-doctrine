<?php

namespace Dimajolkin\YdbDoctrine;

use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;
use YandexCloud\Ydb\Session;
use YandexCloud\Ydb\Traits\TypeValueHelpersTrait;
use Ydb\Type;
use Ydb\TypedValue;

class YdbStatement implements Statement
{
    use TypeValueHelpersTrait;

    /** @var array<int, array<mixed, string> */
    private array $bindValues = [];
    private array $parameters = [];

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
        $index = 1;
        $declareSql = [];
        foreach ($this->bindValues as $param => [$value, $type]) {
            if ($value === null) {
                $rawSql = preg_replace('/\?/', 'NULL', $rawSql, 1);
            }
            else {
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
        if ($type === \Dimajolkin\YdbDoctrine\ParameterType::STRING) {
            return $this->typeValue((string) $value, 'UTF8')->toTypedValue();
        }
        elseif ($type === \Dimajolkin\YdbDoctrine\ParameterType::BINARY) {
            return $this->typeValue((string) $value, 'STRING')->toTypedValue();
        }
        elseif ($type === \Dimajolkin\YdbDoctrine\ParameterType::INTEGER) {
            return $this->typeValue((int) $value, 'INT32')->toTypedValue();
        }
        elseif ($type === \Dimajolkin\YdbDoctrine\ParameterType::BOOLEAN) {
            if ($value === 'true') {
                $value = true;
            }
            elseif ($value === 'false') {
                $value = false;
            }
            else {
                throw new \Exception("Undefined bool value equals $value");
            }

            return $this->typeValue($value, 'BOOL')->toTypedValue();
        }
        elseif ($type === \Dimajolkin\YdbDoctrine\ParameterType::DATETIME) {
            if ($value instanceof \DateTimeImmutable) {
                $value = \DateTime::createFromImmutable($value);
            }

            return $this->typeValue($value, 'DATETIME')->toTypedValue();
        }
        elseif ($type === \Dimajolkin\YdbDoctrine\ParameterType::JSON) {
            return $this->typeValue($value, 'JSON')->toTypedValue();
        }
        elseif ($type === \Dimajolkin\YdbDoctrine\ParameterType::FLOAT) {
            return $this->typeValue($value, 'FLOAT')->toTypedValue();
        }
        elseif ($type === \Dimajolkin\YdbDoctrine\ParameterType::DECIMAL) {
            return $this->typeValue($value, 'FLOAT')->toTypedValue();
        }
        elseif ($type === \Dimajolkin\YdbDoctrine\ParameterType::TIMESTAMP) {
            return $this->typeValue($value, 'TIMESTAMP')->toTypedValue();
        }
        elseif ($type === \Dimajolkin\YdbDoctrine\ParameterType::UINT32) {
            return $this->typeValue($value, 'UINT32')->toTypedValue();
        }
        elseif ($type === \Dimajolkin\YdbDoctrine\ParameterType::UINT64) {
            return $this->typeValue($value, 'UINT64')->toTypedValue();
        }
        throw new \Exception("$type, $value not support");
    }

    public function execute($params = null): Result
    {
        $sql = $this->getRawSql();
        try {
            if (str_starts_with($sql, 'CREATE')) {
                $status = $this->session->schemeQuery($sql);

                return new YdbSchemaResult($status);
            }
            else {
                $res = $this->session->prepare($sql)->execute($this->parameters);

                return new YdbResult($res);
            }
        } catch (\Exception|\Throwable $ex) {
            throw new \Exception($sql."\n".' Details: '.$ex->getMessage(), previous: $ex);
        }
    }
}
