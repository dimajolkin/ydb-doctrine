<?php

namespace Dimajolkin\YdbDoctrine\Tests\Unit\ORM;

use Dimajolkin\YdbDoctrine\Driver\YdbDriver;
use Dimajolkin\YdbDoctrine\ORM\Query;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function testInstanceOf(): void
    {
        $this->createMock(YdbDriver::class); // include the YdbDriver and override Query

        $query = $this->createMock(Query::class);
        $this->assertTrue($query instanceof \Doctrine\ORM\Query);
    }
}
