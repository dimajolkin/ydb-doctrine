<?php

namespace Dimajolkin\YdbDoctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\ParameterType;

class ConnectWrapper extends Connection
{
    public function insert($table, array $data, array $types = []): int|string
    {
        if (count($data) === 0) {
            return $this->executeStatement('INSERT INTO ' . $table . ' () VALUES ()');
        }

        $columns = [];
        $values  = [];
        $set     = [];

        $index = -1;
        foreach ($data as $columnName => $value) {
            $index++;
            $columns[] = $columnName;
            $values[]  = $value;
            $param = '?';
            $type = $types[$index] ?? $types[$columnName] ?? null;
            if ($type && Type::hasType($type)) {
                $doctrineType = Type::getType($type);
                if ($doctrineType->canRequireSQLConversion()) {
                    $param = $doctrineType->convertToDatabaseValueSQL('?', $this->getDatabasePlatform());
                }
            }
            $set[]     = $param;
        }

        return $this->executeStatement(
            'INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ')' .
            ' VALUES (' . implode(', ', $set) . ')',
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
