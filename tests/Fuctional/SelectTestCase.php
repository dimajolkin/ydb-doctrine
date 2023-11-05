<?php

namespace Dimajolkin\YdbDoctrine\Tests\Fuctional;

final class SelectTestCase extends AbstractFunctionalCase
{
    public function testSelectOne(): void
    {
        $res = $this->connection->executeQuery("SELECT 2");

        $this->assertEquals(2, $res->fetchOne());
    }
}