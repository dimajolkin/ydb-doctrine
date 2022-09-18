<?php

namespace Dimajolkin\YdbDoctrine\Tests\Connect;

use Dimajolkin\YdbDoctrine\YdbResult;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Schema\Table;
use PHPUnit\Framework\TestCase;

class ConnectTest extends TestCase
{
    private function ydbConnect()
    {
        return DriverManager::getConnection([
            'driverClass' => \Dimajolkin\YdbDoctrine\YdbDriver::class,
            'wrapperClass' => \Dimajolkin\YdbDoctrine\ConnectWrapper::class
        ]);
    }

    public function testSelectOne(): void
    {
        $conn = $this->ydbConnect();

        $this->assertEquals(2,  $conn->executeQuery('select 2')->fetchOne());
        $this->assertEquals(1, $conn->executeQuery('select 43')->columnCount());
        $this->assertEquals([32], $conn->executeQuery('select 32')->fetchNumeric());
        $this->assertEquals([[43]], $conn->executeQuery('select 43')->fetchAllNumeric());
        $this->assertEquals([1 => 2], $conn->executeQuery('select 1, 2')->fetchAllKeyValue());
        $this->assertEquals([12], $conn->executeQuery('select 12, 43')->fetchFirstColumn());

        $this->assertEquals([['column0' => 12]], $conn->executeQuery('select 12')->fetchAllAssociative());
        $this->assertEquals(['column0' => 12], $conn->executeQuery('select 12')->fetchAssociative());

    }

    private function query(string $sql): Result
    {
        $conn = $this->ydbConnect();;
        /** @var YdbResult $res */
        return $conn->executeQuery($sql);
    }

    public function testSelectTable(): void
    {
        $this->assertEquals(1,  $this->query('select id, name from my_table')->fetchOne());
        $this->assertEquals([['id' => 1, 'name' => 'dima'], ['id' => 2, 'name' => 'nadia']],  $this->query('select id, name from my_table where id in (1, 2)')->fetchAllAssociative());
    }

    public function testInsert()
    {
        $conn = $this->ydbConnect();
        $conn->delete('my_table', ['name' => 'ivan/test']);
        $this->assertEquals(false,  $this->query('select id, name from my_table where name = \'ivan\/test\'')->fetchAssociative());
        $data = ['id' => 7, 'name' => 'ivan/test'];
        $conn->insert('my_table', $data, ['id' => ParameterType::INTEGER, 'name' => ParameterType::STRING]);
        $this->assertEquals($data,  $this->query('select id, name from my_table where name = \'ivan\/test\'')->fetchAssociative());
    }

    public function testCreateTableMigration()
    {
        $sql = 'CREATE TABLE doctrine_migration_versions2 (version String NOT NULL, executed_at Datetime, execution_time Int32, PRIMARY KEY(version));';
        $this->query($sql);
    }
}
