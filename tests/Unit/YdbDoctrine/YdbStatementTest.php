<?php

namespace Dimajolkin\YdbDoctrine\Tests\Unit\YdbDoctrine;

use Dimajolkin\YdbDoctrine\Driver\YdbConnection;
use Dimajolkin\YdbDoctrine\YdbStatement;
use Doctrine\DBAL\ParameterType;
use PHPUnit\Framework\TestCase;
use YdbPlatform\Ydb\Session;
use YdbPlatform\Ydb\Table;
use YdbPlatform\Ydb\Ydb;

class YdbStatementTest extends TestCase
{
    private function makeYdb(): Ydb
    {
        $ydb = $this->createMock(Ydb::class);
        $table = $this->createMock(Table::class);
        $session = $this->createMock(Session::class);
        $table->method('session')->willReturn($session);
        $ydb->method('table')->willReturn($table);

        return $ydb;
    }

    public function testString(): void
    {
        $ydb = $this->makeYdb();
        $connect = new YdbConnection($ydb);
        $statement = new YdbStatement($connect, 'INSERT INTO my_table (name) VALUES (?)', $ydb->table());
        $statement->bindValue(1, 'name\test2');
        $this->assertEquals("DECLARE \$col1 AS UTF8;\nINSERT INTO my_table (name) VALUES (\$col1)", $statement->getRawSql());
    }

    public function testInsertMigration(): void
    {
        $ydb = $this->makeYdb();

        $connect = new YdbConnection($ydb);
        $statement = new YdbStatement(
            $connect,
            'INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES (?, ?, ?)',
            $ydb->table(),
        );
        $statement->bindValue(1, 'DoctrineMigrations\Version20211102143635', ParameterType::STRING);
        $statement->bindValue(2, '2022-09-18 16:12:36', ParameterType::STRING);
        $statement->bindValue(3, 38, ParameterType::INTEGER);
        $expectedSql = <<<SQL
DECLARE \$col1 AS UTF8;
DECLARE \$col2 AS UTF8;
DECLARE \$col3 AS INT32;
INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES (\$col1, \$col2, \$col3)
SQL;

        $this->assertEquals($expectedSql, $statement->getRawSql());
    }
}
