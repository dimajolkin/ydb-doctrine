<?php

namespace Dimajolkin\YdbDoctrine;

use Dimajolkin\YdbDoctrine\Driver\YdbDriver;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\Type;

final class YdbConnection extends Connection
{
    public function __construct(#[\SensitiveParameter] array $params, Driver $driver, ?Configuration $config = null, ?EventManager $eventManager = null)
    {
        if (! $driver instanceof YdbDriver) {
            throw new \InvalidArgumentException('The driver must be an instance of YdbDriver');
        }

        parent::__construct($params, $driver, $config, $eventManager);
    }


    public function insert($table, array $data, array $types = []): int|string
    {
        if (0 === count($data)) {
            return $this->executeStatement('INSERT INTO '.$table.' () VALUES ()');
        }

        $columns = [];
        $values = [];
        $set = [];

        $index = -1;
        foreach ($data as $columnName => $value) {
            ++$index;
            $columns[] = $columnName;
            $values[] = $value;
            $param = '?';
            $type = $types[$index] ?? $types[$columnName] ?? null;
            if ($type && Type::hasType($type)) {
                $doctrineType = Type::getType($type);
                if ($doctrineType->canRequireSQLConversion()) {
                    $param = $doctrineType->convertToDatabaseValueSQL('?', $this->getDatabasePlatform());
                }
            }
            $set[] = $param;
        }

        return $this->executeStatement(
            'INSERT INTO '.$table.' ('.implode(', ', $columns).')'.
            ' VALUES ('.implode(', ', $set).')',
            $values,
            is_string(key($types)) ? $this->extractTypeValues($columns, $types) : $types,
        );
    }

    private function extractTypeValues(array $columnList, array $types): array
    {
        $typeValues = [];
        foreach ($columnList as $columnName) {
            $typeValues[] = $types[$columnName] ?? ParameterType::STRING;
        }

        return $typeValues;
    }
}
