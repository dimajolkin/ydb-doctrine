<?php

namespace Dimajolkin\YdbDoctrine\Tests\Fuctional;

use Dimajolkin\YdbDoctrine\Driver\YdbDriver;
use Dimajolkin\YdbDoctrine\YdbConnection;
use PHPUnit\Framework\TestCase;

abstract class AbstractFunctionalCase extends TestCase
{
    protected YdbConnection $connection;

    public function setUp(): void
    {
        $this->connection = new YdbConnection(['url' => $_ENV['YDB_URL']], new YdbDriver());
        $this->connection->connect();
        $this->connection->beginTransaction();
    }

    public function tearDown(): void
    {
        $this->connection->rollBack();
        $this->connection->close();
    }
}