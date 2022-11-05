<?php

namespace Dimajolkin\YdbDoctrine\Tests\ORM\Parse;

use Dimajolkin\YdbDoctrine\Tests\App\Entity\User;
use Dimajolkin\YdbDoctrine\YdbPlatform;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use PHPUnit\Framework\TestCase;

class SelectParseTest extends AbstractParseTest
{
    public function testParse(): void
    {
        $em = $this->makeEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u')->from(User::class, 'u')->andWhere($qb->expr()->eq('u.id', 1));

        $this->assertEquals('SELECT u FROM Dimajolkin\YdbDoctrine\Tests\App\Entity\User u WHERE u.id = 1', $qb->getDQL());
        $this->assertEquals('SELECT u0_.id AS id_0 FROM User u0_ WHERE u0_.id = 1', $qb->getQuery()->getSQL());
    }

    public function testOrderBy(): void
    {
        $em = $this->makeEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u')->from(User::class, 'u')->orderBy('u.id', 'ASC');

        $this->assertEquals('SELECT u FROM Dimajolkin\YdbDoctrine\Tests\App\Entity\User u ORDER BY u.id ASC', $qb->getDQL());
        $this->assertEquals('SELECT u0_.id AS id_0 FROM User u0_ ORDER BY u0_.id_0 ASC', $qb->getQuery()->getSQL());
    }
}
