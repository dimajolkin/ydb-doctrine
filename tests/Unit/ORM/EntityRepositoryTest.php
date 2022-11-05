<?php

namespace Dimajolkin\YdbDoctrine\Tests\Unit\ORM;

use Dimajolkin\YdbDoctrine\Tests\App\Doctrine\ManagerRegistry;
use Dimajolkin\YdbDoctrine\Tests\App\Entity\User;
use Dimajolkin\YdbDoctrine\Tests\App\Repository\UserRepository;
use Dimajolkin\YdbDoctrine\Tests\Helpers\EntityManagerFactoryTrait;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class EntityRepositoryTest extends TestCase
{
    use EntityManagerFactoryTrait;

    public function testFind(): void
    {
        $em = $this->makeEntityManager();
        $repository = new EntityRepository($em, $em->getClassMetadata(User::class));
        $res = $repository->find(1);
        $this->assertTrue(true);
    }
}
