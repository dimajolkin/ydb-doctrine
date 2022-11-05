<?php

namespace Dimajolkin\YdbDoctrine\Tests\ORM\Parse;

use Dimajolkin\YdbDoctrine\Tests\App\Entity\User;

class RandFunctionTest extends AbstractParseTest
{
    public function testParse(): void
    {
        $em = $this->makeEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u')->from(User::class, 'u')->orderBy('RAND(u.id)');

        $this->assertEquals('SELECT u FROM Dimajolkin\YdbDoctrine\Tests\App\Entity\User u ORDER BY RAND(u.id) ASC', $qb->getDQL());
        $this->assertEquals('SELECT u0_.id AS id_0 FROM User u0_ ORDER BY RANDOM(u0_.id_0) ASC', $qb->getQuery()->getSQL());
    }
}
