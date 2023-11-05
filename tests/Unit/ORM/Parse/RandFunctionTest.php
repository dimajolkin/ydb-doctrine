<?php

namespace Dimajolkin\YdbDoctrine\Tests\Unit\ORM\Parse;

use Dimajolkin\YdbDoctrine\Tests\App\Entity\SimpleEntity;

class RandFunctionTest extends AbstractParse
{
    public function testParse(): void
    {
        $em = $this->makeEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u')->from(SimpleEntity::class, 'u')->orderBy('RAND(u.id)');

        $this->assertEquals('SELECT u FROM Dimajolkin\YdbDoctrine\Tests\App\Entity\SimpleEntity u ORDER BY RAND(u.id) ASC', $qb->getDQL());
        $this->assertEquals('SELECT s0_.id AS id_0 FROM simple_entity s0_ ORDER BY RANDOM(id_0) ASC', $qb->getQuery()->getSQL());
    }
}
