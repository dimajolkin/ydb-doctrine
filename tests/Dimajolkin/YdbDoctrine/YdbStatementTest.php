<?php

namespace Dimajolkin\YdbDoctrine;

use Dimajolkin\YdbDoctrine\Yql\YqlFixer;
use Doctrine\DBAL\ParameterType;
use PHPUnit\Framework\TestCase;
use YandexCloud\Ydb\Session;
use YandexCloud\Ydb\Ydb;

class YdbStatementTest extends TestCase
{
    public function testBindValue(): void
    {
        $ydb = $this->createMock(Ydb::class);
        $connect = new YdbConnection($ydb);
        $session = $this->createMock(Session::class);
        $statement = new YdbStatement($connect, 'INSERT INTO my_table (name, value, age) VALUES (?, ?, ?)', $session);
        $statement->bindValue(1, 'name');
        $statement->bindValue(2, 'value');
        $statement->bindValue(3, 23, ParameterType::INTEGER);

        $this->assertEquals('INSERT INTO my_table (name, value, age) VALUES (\'name\', \'value\', 23)', $statement->getRawSql());
    }

    public function testString(): void
    {
        $driver = new YdbDriver();
        $driver->configureType();

        $ydb = $this->createMock(Ydb::class);
        $connect = new YdbConnection($ydb);
        $session = $this->createMock(Session::class);
        $statement = new YdbStatement($connect, 'INSERT INTO my_table (name) VALUES (?)', $session);
        $statement->bindValue(1, 'name\test2');

        $this->assertEquals("INSERT INTO my_table (name) VALUES ('name\\test2')", $statement->getRawSql());
    }

    public function testInsertMigration(): void
    {
        $ydb = $this->createMock(Ydb::class);
        $connect = new YdbConnection($ydb);
        $session = $this->createMock(Session::class);
        $statement = new YdbStatement($connect, 'INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES (?, ?, ?)', $session);
        $statement->bindValue(1, 'DoctrineMigrations\Version20211102143635', ParameterType::STRING);
        $statement->bindValue(2, '2022-09-18 16:12:36', ParameterType::STRING);
        $statement->bindValue(3, 38, ParameterType::INTEGER);

        $this->assertEquals("INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\\\\Version20211102143635', '2022-09-18 16:12:36', 38)", $statement->getRawSql());
    }

    public function testRegex()
    {
        $this->assertEquals('DoctrineMigrations\Version20220918090353, ?', preg_replace('/\?/', 'DoctrineMigrations\Version20220918090353', '?, ?', 1));
    }


    public function testOrderByFixed(): void
    {
        $fixed = new YqlFixer();
        $this->assertEquals(
            'select name as name1 form table order by name1 desc',
            $fixed->fixed('select name as name1 form table order by name desc')
        );
    }
}
