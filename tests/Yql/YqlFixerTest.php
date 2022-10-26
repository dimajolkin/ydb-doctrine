<?php

namespace Yql;

use Dimajolkin\YdbDoctrine\Yql\YqlFixer;
use PHPUnit\Framework\TestCase;

class YqlFixerTest extends TestCase
{
    public function providerSql(): array
    {
        return [
            [
                'SELECT DISTINCT u0_.id AS id, u0_.id AS id FROM user_order u0_ ORDER BY u0_.id DESC LIMIT 20',
                'SELECT DISTINCT u0_.id AS id FROM user_order u0_ ORDER BY u0_.id DESC LIMIT 20',
            ],
        ];
    }

    /**
     * @dataProvider providerSql
     */
    public function testDistinct(string $actual, string $expected): void
    {
        $this->assertEquals($expected, (new YqlFixer())->fixed($actual));
    }
}
