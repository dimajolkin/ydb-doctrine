<?php

namespace Dimajolkin\YdbDoctrine\Tests\App\Entity;

use Dimajolkin\YdbDoctrine\Tests\App\Repository\UserRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

#[Entity(repositoryClass: UserRepository::class)]
class User
{
    #[Id()]
    #[Column(type: 'id')]
    public int $id;
}
