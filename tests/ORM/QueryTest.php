<?php

namespace Dimajolkin\YdbDoctrine\Tests\ORM;

use Dimajolkin\YdbDoctrine\ORM\Query;
use Doctrine\ORM\AbstractQuery;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function testInstanceOf(): void
    {
        $query = $this->createMock(Query::class);
        $this->assertTrue($query instanceof \Doctrine\ORM\Query);
    }
}
