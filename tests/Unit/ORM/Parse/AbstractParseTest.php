<?php

namespace Dimajolkin\YdbDoctrine\Tests\Unit\ORM\Parse;

use Dimajolkin\YdbDoctrine\ORM\EntityManager;
use Dimajolkin\YdbDoctrine\ORM\Functions\Rand;
use Dimajolkin\YdbDoctrine\Tests\Helpers\EntityManagerFactoryTrait;
use Dimajolkin\YdbDoctrine\YdbConnection;
use Dimajolkin\YdbDoctrine\YdbPlatform;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use PHPUnit\Framework\TestCase;

abstract class AbstractParseTest extends TestCase
{
    use EntityManagerFactoryTrait;
}
