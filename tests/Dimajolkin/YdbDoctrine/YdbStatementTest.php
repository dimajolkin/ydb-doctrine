<?php

namespace Dimajolkin\YdbDoctrine;

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
}
