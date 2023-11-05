<?php

namespace Dimajolkin\YdbDoctrine\Tests\Fuctional\Entity;

use Dimajolkin\YdbDoctrine\Tests\App\Entity\User;
use Dimajolkin\YdbDoctrine\Tests\Fuctional\AbstractFunctionalCase;

class UserEntityTestCase extends AbstractFunctionalCase
{
    public function testCreate(): void
    {
        $em = $this->createEntityManager();
        $this->generateSchema($em, [
            User::class,
        ]);

        $user = new User(1, 'Ivan', 43, new \DateTimeImmutable(), true, false);
        $em->persist($user);
        $em->flush();

        $user2 = $em->find(User::class, 1);
        $this->assertNotEmpty($user2);

        $this->assertEquals($user->id, $user2->id);
        $this->assertEquals($user->name, $user2->name);
        $this->assertEquals($user->age, $user2->age);
        $this->assertEquals($user->createAt, $user2->createAt);
        $this->assertEquals($user->active, $user2->active);
        $this->assertEquals($user->delete, $user2->delete);
    }
}