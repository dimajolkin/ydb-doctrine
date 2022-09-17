<?php

namespace Dimajolkin\YdbDoctrine\Tests\Connect;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Table;
use PHPUnit\Framework\TestCase;

class ConnectTest extends TestCase
{
    private function ydbConnect()
    {
        return DriverManager::getConnection([
            'driverClass' => \Dimajolkin\YdbDoctrine\YdbDriver::class,
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

    public function testSelectTable(): void
    {
        $connectionParams = [
//            'dbname' => 'postgres',
//            'user' => 'sam',
//            'password' => 'Rbhgbxb44',
//            'host' => 'localhost',
//            'driver' => 'pdo_pgsql',
                'driverClass' => \Dimajolkin\YdbDoctrine\YdbDriver::class,
        ];

        $conn = DriverManager::getConnection($connectionParams);

        $this->assertTrue($conn->createSchemaManager()->tablesExist('users'));

//        $this->assertEquals(2,  $conn->executeQuery('select id, name from users')->fetchOne());
//        $this->assertEquals(1, $conn->executeQuery('select 43')->columnCount());
//        $this->assertEquals([32], $conn->executeQuery('select 32')->fetchNumeric());
//        $this->assertEquals([[43]], $conn->executeQuery('select 43')->fetchAllNumeric());
//        $this->assertEquals([1 => 2], $conn->executeQuery('select 1, 2')->fetchAllKeyValue());
//        $this->assertEquals([12], $conn->executeQuery('select 12, 43')->fetchFirstColumn());
//
//        $this->assertEquals([['column0' => 12]], $conn->executeQuery('select 12')->fetchAllAssociative());
//        $this->assertEquals(['column0' => 12], $conn->executeQuery('select 12')->fetchAssociative());

    }
}
