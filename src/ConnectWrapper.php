<?php

namespace Dimajolkin\YdbDoctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class ConnectWrapper extends Connection
{
    public function insert($table, array $data, array $types = [])
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
            if (isset($types[$index])) {
                $doctrineType = Type::getType($types[$index]);
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
