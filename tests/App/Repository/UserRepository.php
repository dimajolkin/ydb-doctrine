<?php

namespace Dimajolkin\YdbDoctrine\Tests\App\Repository;

use Dimajolkin\YdbDoctrine\Tests\App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityClass = User::class;
        $manager = $registry->getManagerForClass($entityClass);
        parent::__construct($manager, $manager->getClassMetadata($entityClass));
    }
}
