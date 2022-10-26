<?php

namespace Dimajolkin\YdbDoctrine;

use Dimajolkin\YdbDoctrine\Excepion\GenerateSqlException;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;
use Google\Protobuf\NullValue;
use YandexCloud\Ydb\Session;
use YandexCloud\Ydb\Traits\TypeValueHelpersTrait;
use YandexCloud\Ydb\Types\DatetimeType;
use YandexCloud\Ydb\Types\IntType;
use Ydb\Type;
use Ydb\TypedValue;
use Ydb\Value;

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
    ) {}

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
            } else {
                $name = '$col'.$index++;
                $rawSql = preg_replace('/\?/', $name, $rawSql, 1);
                $type = $this->makeYdbType($value, $type);
                $this->parameters[$name] = $type;
                $declareSql[] = sprintf("DECLARE $name AS %s;\n",  Type\PrimitiveTypeId::name($type->getType()->getTypeId()));
            }
        }

        return implode($declareSql).''.$rawSql;
    }

    private function makeYdbType($value, $type): TypedValue
    {
        if ($type === ParameterType::STRING) {
            return $this->typeValue((string) $value, 'UTF8')->toTypedValue();
        }
        elseif ($type === ParameterType::INTEGER) {
            return $this->typeValue((int) $value, 'INT32')->toTypedValue();
        }
        elseif ($type === ParameterType::BOOLEAN) {
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
        throw new \Exception();
    }

    public function execute($params = null): Result
    {
        $sql = $this->getRawSql();

        return $this->session->transaction(function () use ($sql) {
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
                dd($this->sql);
                throw new \Exception($sql."\n".' Details: '.$ex->getMessage(), previous: $ex);
            }
        });
    }
}
