<?php

namespace Dimajolkin\YdbDoctrine\Tests\Unit\ORM\Parse;

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
        $this->assertEquals('SELECT u0_.id AS id_0 FROM user u0_ WHERE u0_.id = 1', $qb->getQuery()->getSQL());
    }

    public function testOrderBy(): void
    {
        $em = $this->makeEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u')->from(User::class, 'u')->orderBy('u.id', 'ASC');

        $this->assertEquals('SELECT u FROM Dimajolkin\YdbDoctrine\Tests\App\Entity\User u ORDER BY u.id ASC', $qb->getDQL());
        $this->assertEquals('SELECT u0_.id AS id_0 FROM user u0_ ORDER BY u0_.id_0 ASC', $qb->getQuery()->getSQL());
    }

    public function testOrderByWithLimit(): void
    {
        $em = $this->makeEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->distinct()
            ->select('u')
            ->addSelect('u.id')
            ->from(User::class, 'u')
            ->orderBy('u.id')
            ->setMaxResults(20);

        $this->assertEquals('SELECT DISTINCT u, u.id FROM Dimajolkin\YdbDoctrine\Tests\App\Entity\User u ORDER BY u.id ASC', $qb->getDQL());
        $this->assertEquals('SELECT DISTINCT u0_.id AS id_0, u0_.id AS id_1 FROM user u0_ ORDER BY u0_.id_0 ASC LIMIT 20', $qb->getQuery()->getSQL());
    }


    public function testParseDQL(): void
    {
        $em = $this->makeEntityManager();
        $query = $em->createQuery('SELECT entity FROM Dimajolkin\YdbDoctrine\Tests\App\Entity\User entity ORDER BY entity.id DESC');
        $this->assertEquals('SELECT u0_.id AS id_0 FROM user u0_ ORDER BY u0_.id_0 DESC', $query->getSQL());

        $cloneQuery = clone $query;
        $cloneQuery->setMaxResults(10);
        $cloneQuery->setFirstResult(11);
        $this->assertEquals('SELECT u0_.id AS id_0 FROM user u0_ ORDER BY u0_.id_0 DESC LIMIT 10 OFFSET 11', $cloneQuery->getSQL());
    }
}
