<?php

namespace Dimajolkin\YdbDoctrine;

use Dimajolkin\YdbDoctrine\Excepion\GenerateSqlException;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;
use YandexCloud\Ydb\Session;
use Ydb\Type;
use Ydb\TypedValue;
use Ydb\Value;

class YdbStatement implements Statement
{

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
            $name = '$col'.$index++;
            //            $rawSql = preg_replace('/\?/', $this->connection->quote($value, $type), $rawSql, 1);
            $rawSql = preg_replace('/\?/', $name, $rawSql, 1);
            $type = $this->makeYdbType($value, $type);
            $this->parameters[$name] = $type;
            $declareSql[] =
                sprintf("DECLARE $name AS %s;\n", Type\PrimitiveTypeId::name($type->getType()->getTypeId()));
        }

        return implode($declareSql).''.$rawSql;
    }

    private function makeYdbType($value, $type): TypedValue
    {
        if ($type === ParameterType::STRING) {
            return new TypedValue([
                'type' => new Type(['type_id' => Type\PrimitiveTypeId::UTF8]),
                'value' => new Value(['text_value' => (string) $value]),
            ]);
        }
        elseif ($type === ParameterType::INTEGER) {
            return new TypedValue([
                'type' => new Type(['type_id' => Type\PrimitiveTypeId::INT32]),
                'value' => new Value(['int32_value' => (int) $value]),
            ]);
        }
        elseif ($type === ParameterType::BOOLEAN) {
            return new TypedValue([
                'type' => new Type(['type_id' => Type\PrimitiveTypeId::BOOL]),
                'value' => new Value(['bool_value' => !!$value]),
            ]);
        }
        elseif ($type === ParameterType::BOOLEAN) {
            return new TypedValue([
                'type' => new Type(['type_id' => Type\PrimitiveTypeId::BOOL]),
                'value' => new Value(['bool_value' => !!$value]),
            ]);
        }
        elseif ($type === \Dimajolkin\YdbDoctrine\ParameterType::DATETIME) {
            return new TypedValue([
                'type' => new Type(['type_id' => Type\PrimitiveTypeId::DATETIME]),
                'value' => new Value(['uint32_value' => (int) $value]),
            ]);
        }
        elseif ($type === \Dimajolkin\YdbDoctrine\ParameterType::JSON) {
            return new TypedValue([
                'type' => new Type(['type_id' => Type\PrimitiveTypeId::JSON]),
                'value' => new Value(['text_value' => $value]),
            ]);
        }
        elseif ($type === \Dimajolkin\YdbDoctrine\ParameterType::FLOAT) {
            return new TypedValue([
                'type' => new Type(['type_id' => Type\PrimitiveTypeId::FLOAT]),
                'value' => new Value(['float_value' => (float) $value]),
            ]);
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

                    $res = $this->session->query($sql, $this->parameters);

                    return new YdbResult($res);
                }
            } catch (\Exception $ex) {
                throw new \Exception($sql."\n".' Details: '.$ex->getMessage(), previous: $ex);
            }
        });
    }
}
