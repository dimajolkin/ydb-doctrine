<?php

namespace Dimajolkin\YdbDoctrine;

use Dimajolkin\YdbDoctrine\Yql\YqlFixer;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
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
        $this->assertEquals(
            'INSERT INTO my_table (name, value, age) VALUES (\'name\', \'value\', 23)',
            $statement->getRawSql()
        );
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
        $statement = new YdbStatement(
            $connect,
            'INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES (?, ?, ?)',
            $session
        );
        $statement->bindValue(1, 'DoctrineMigrations\Version20211102143635', ParameterType::STRING);
        $statement->bindValue(2, '2022-09-18 16:12:36', ParameterType::STRING);
        $statement->bindValue(3, 38, ParameterType::INTEGER);
        $this->assertEquals(
            "INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\\\\Version20211102143635', '2022-09-18 16:12:36', 38)",
            $statement->getRawSql()
        );
    }


    public function testInsert(): void
    {
        $insertSql =
            "INSERT INTO event (id, title, date, for_who, description, destination, program, address, type, like_likes, like_dislikes, date_create, date_deleting, image_id, date_finish, hidden, slug) VALUES (INSERT INTO event (id, title, date, for_who, description, destination, program, address, type, like_likes, like_dislikes, date_create, date_deleting, image_id, date_finish, hidden, slug) VALUES (?, ?, DateTime::MakeDate(DateTime::ParseIso8601(?)), ?, ?, ?, ?, ?, ?, ?, ?, DateTime::MakeDate(DateTime::ParseIso8601(?)), DateTime::MakeDate(DateTime::ParseIso8601(?)), ?, DateTime::MakeDate(DateTime::ParseIso8601(?)), ?, ?))";
        $this->assertEquals(17, substr_count($insertSql, '?'));
        $ydb = $this->createMock(Ydb::class);
        $connect = new YdbConnection($ydb);
        $session = $this->createMock(Session::class);
        $statement = new YdbStatement($connect, $insertSql, $session);
        $statement->bindValue(1, '36');
        $statement->bindValue(2, '"Идеальная реставрация');
        $statement->bindValue(3, '2021-02-13 03:00:00');
        $statement->bindValue(4, 'Курс для терапевтов, стоматологов, студентов, ординаторов');
        $statement->bindValue(5, '<div>text?</div>');
        $statement->bindValue(6, '<div>text?</div>');
        $statement->bindValue(7, '<div>text?</div>');
        $statement->bindValue(8, 'г. Москва, ул. Островитянова 43');
        $statement->bindValue(9, 1, ParameterType::INTEGER);
        $statement->bindValue(10, 1, ParameterType::INTEGER);
        $statement->bindValue(11, 1, ParameterType::INTEGER);
        $statement->bindValue(12, '2022-02-01 13:08:54', ParameterType::INTEGER);
        $statement->bindValue(13, null);
        $statement->bindValue(14, '19');
        $statement->bindValue(15, '2021-02-13 08:00:00');
        $statement->bindValue(16, 'true', ParameterType::BOOLEAN);
        $statement->bindValue(17, 'idealnaia-restavratsiia-2022-02-01-10-08');
        $prepareSql = $statement->getRawSql();

        $this->assertEquals(0, substr_count($prepareSql, '?'));
    }

    public function testRegex()
    {
        $this->assertEquals(
            'DoctrineMigrations\Version20220918090353, ?',
            preg_replace('/\?/', 'DoctrineMigrations\Version20220918090353', '?, ?', 1)
        );
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
