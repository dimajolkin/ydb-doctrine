<?php

namespace Dimajolkin\YdbDoctrine\Tests\App\Entity;

use Dimajolkin\YdbDoctrine\Tests\App\Repository\UserRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Table(name: 'simple_entity')]
#[Entity(repositoryClass: UserRepository::class)]
class SimpleEntity
{
    #[Id()]
    #[Column(type: 'integer')]
    public int $id;
}