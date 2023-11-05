<?php

namespace Dimajolkin\YdbDoctrine\Tests\Unit\ORM\Parse;

use Dimajolkin\YdbDoctrine\Tests\App\Entity\SimpleEntity;

class SelectParseTest extends AbstractParse
{
    public function testParse(): void
    {
        $em = $this->makeEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u')->from(SimpleEntity::class, 'u')->andWhere($qb->expr()->eq('u.id', 1));

        $this->assertEquals('SELECT u FROM Dimajolkin\YdbDoctrine\Tests\App\Entity\SimpleEntity u WHERE u.id = 1', $qb->getDQL());
        $this->assertEquals('SELECT s0_.id AS id_0 FROM simple_entity s0_ WHERE s0_.id = 1', $qb->getQuery()->getSQL());
    }

    public function testOrderBy(): void
    {
        $em = $this->makeEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u')->from(SimpleEntity::class, 'u')->orderBy('u.id', 'ASC');

        $this->assertEquals('SELECT u FROM Dimajolkin\YdbDoctrine\Tests\App\Entity\SimpleEntity u ORDER BY u.id ASC', $qb->getDQL());
        $this->assertEquals('SELECT s0_.id AS id_0 FROM simple_entity s0_ ORDER BY id_0 ASC', $qb->getQuery()->getSQL());
    }

    public function testOrderByWithLimit(): void
    {
        $em = $this->makeEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->distinct()
            ->select('u')
            ->addSelect('u.id')
            ->from(SimpleEntity::class, 'u')
            ->orderBy('u.id')
            ->setMaxResults(20);

        $this->assertEquals('SELECT DISTINCT u, u.id FROM Dimajolkin\YdbDoctrine\Tests\App\Entity\SimpleEntity u ORDER BY u.id ASC', $qb->getDQL());
        $this->assertEquals('SELECT DISTINCT s0_.id AS id_0, s0_.id AS id_1 FROM simple_entity s0_ ORDER BY id_0 ASC LIMIT 20', $qb->getQuery()->getSQL());
    }

    public function testParseDQL(): void
    {
        $em = $this->makeEntityManager();
        $query = $em->createQuery('SELECT entity FROM Dimajolkin\YdbDoctrine\Tests\App\Entity\SimpleEntity entity ORDER BY entity.id DESC');
        $this->assertEquals('SELECT s0_.id AS id_0 FROM simple_entity s0_ ORDER BY id_0 DESC', $query->getSQL());

        $cloneQuery = clone $query;
        $cloneQuery->setMaxResults(10);
        $cloneQuery->setFirstResult(11);
        $this->assertEquals('SELECT s0_.id AS id_0 FROM simple_entity s0_ ORDER BY id_0 DESC LIMIT 10 OFFSET 11', $cloneQuery->getSQL());
    }
}
