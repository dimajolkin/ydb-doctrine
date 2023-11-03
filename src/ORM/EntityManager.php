<?php

namespace Dimajolkin\YdbDoctrine\ORM;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Decorator\EntityManagerDecorator;
use Doctrine\ORM\QueryBuilder;

class EntityManager extends EntityManagerDecorator
{
    public function __construct(Connection $conn, Configuration $config, EventManager $eventManager = null)
    {
        $entityManager = \Doctrine\ORM\EntityManager::create($conn, $config, $eventManager);
        parent::__construct($entityManager);
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this);
    }

    public function createQuery($dql = ''): Query
    {
        $query = new Query($this);
        if (!empty($dql)) {
            $query->setDQL($dql);
        }

        return $query;
    }
}
