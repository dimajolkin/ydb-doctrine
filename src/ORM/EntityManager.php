<?php

namespace Dimajolkin\YdbDoctrine\ORM;

use Doctrine\Common\EventManager;
use Doctrine\ORM\Configuration;

class EntityManager extends \Doctrine\ORM\EntityManager
{
    public static function create($connection, Configuration $config, ?EventManager $eventManager = null): EntityManager
    {
        $connection = static::createConnection($connection, $config, $eventManager);

        return new EntityManager($connection, $config);
    }

    /**
     * {@inheritDoc}
     */
    public function createQuery($dql = '')
    {
        $query = new Query($this);

        if (! empty($dql)) {
            $query->setDQL($dql);
        }

        return $query;
    }
}
