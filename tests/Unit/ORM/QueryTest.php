<?php

namespace Dimajolkin\YdbDoctrine\Tests\Unit\ORM;

use Dimajolkin\YdbDoctrine\ORM\Query;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function testInstanceOf(): void
    {
        $query = $this->createMock(Query::class);
        $this->assertTrue($query instanceof \Doctrine\ORM\Query);
    }
}
