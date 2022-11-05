<?php

namespace Dimajolkin\YdbDoctrine\ORM;

class EntityManager extends \Doctrine\ORM\EntityManager
{
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
