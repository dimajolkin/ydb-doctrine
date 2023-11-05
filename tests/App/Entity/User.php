<?php

namespace Dimajolkin\YdbDoctrine\Tests\App\Entity;

use DateTimeImmutable;
use Dimajolkin\YdbDoctrine\Tests\App\Repository\UserRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Table(name: 'user')]
#[Entity(repositoryClass: UserRepository::class)]
class User
{
    #[Id()]
    #[Column(type: 'integer')]
    public int $id;

    #[Column(type: 'text', nullable: true)]
    public string $name;

    #[Column(type: 'integer', nullable: true)]
    public int $age;

    #[Column(type: 'datetimetz', nullable: true)]
    public DateTimeImmutable $createAt;

    #[Column(type: 'boolean', nullable: true)]
    public bool $active;

    #[Column(type: 'boolean', nullable: true)]
    public bool $delete;

    public function __construct(
        int $id,
        string $name,
        int $age,
        DateTimeImmutable $createAt,
        bool $active,
        bool $delete,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->age = $age;
        $this->createAt = $createAt;
        $this->active = $active;
        $this->delete = $delete;
    }
}
