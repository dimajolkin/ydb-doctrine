<?php

namespace Dimajolkin\YdbDoctrine\Tests\Fuctional;

use Dimajolkin\YdbDoctrine\Driver\YdbDriver;
use Dimajolkin\YdbDoctrine\ORM\EntityManager;
use Dimajolkin\YdbDoctrine\Tests\App\Entity\User;
use Dimajolkin\YdbDoctrine\YdbConnection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use PHPUnit\Framework\TestCase;

abstract class AbstractFunctionalCase extends TestCase
{
    protected YdbConnection $connection;

    public function setUp(): void
    {
        $this->connection = new YdbConnection(['url' => $_ENV['YDB_URL']], new YdbDriver());
        $this->connection->connect();
        $this->connection->beginTransaction();
    }

    public function tearDown(): void
    {
        $this->connection->rollBack();
        $this->connection->close();
    }

    protected function createEntityManager(): EntityManagerInterface
    {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: array(__DIR__."/../App"),
            isDevMode: true,
        );

        return new EntityManager($this->connection, $config);
    }

    public function generateSchema(EntityManagerInterface $em, array $entityClasses): void
    {
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = [];
        foreach ($entityClasses as $className) {
            $classes = [$em->getClassMetadata($className)];
        }

        $tool->dropSchema($classes);
        $tool->createSchema($classes);
    }
}