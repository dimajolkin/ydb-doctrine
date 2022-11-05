<?php

namespace Dimajolkin\YdbDoctrine\Tests\ORM\Parse;

use Dimajolkin\YdbDoctrine\ORM\EntityManager;
use Dimajolkin\YdbDoctrine\ORM\Functions\Rand;
use Dimajolkin\YdbDoctrine\YdbPlatform;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use PHPUnit\Framework\TestCase;

abstract class AbstractParseTest extends TestCase
{
    protected function makeEntityManager(): EntityManager
    {
        $connect = $this->createMock(Connection::class);
        $connect->method('getDatabasePlatform')->willReturn(new YdbPlatform());
        $connect->method('getEventManager')->willReturn(new EventManager());

        $configuration = new Configuration();
        $configuration->addCustomStringFunction('RAND', Rand::class);
        $configuration->setMetadataDriverImpl(new AttributeDriver([__DIR__ . '/App/Entity']));
        $configuration->setProxyDir(__DIR__ . '/App');
        $configuration->setProxyNamespace('App');

        return new EntityManager($connect, $configuration);
    }
}
