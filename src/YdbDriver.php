<?php

namespace Dimajolkin\YdbDoctrine;

use Dimajolkin\YdbDoctrine\SchemaManager\YdbSchemaManager;
use Dimajolkin\YdbDoctrine\Type\DateTimeType;
use Dimajolkin\YdbDoctrine\Type\DateTimeTzType;
use Dimajolkin\YdbDoctrine\Type\DecimalType;
use Dimajolkin\YdbDoctrine\Type\FloatType;
use Dimajolkin\YdbDoctrine\Type\JsonType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\API\ExceptionConverter;
use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Psr\Log\LoggerInterface;
use YandexCloud\Ydb\Ydb;

/**
 * Версия файла без final атрибота и с другим классом Parser
 */
include_once __DIR__ . '/../doctrine/Query.php';

class YdbDriver implements Driver
{
    private ?Ydb $ydb = null;
    private ?LoggerInterface $logger = null;

    public function connect(array $params): DriverConnection
    {
        $dbUri = $params['driverOptions']['YBD_URL'] ?? throw new \Exception();
        $this->overrideBaseTypes();
        $connect = YdbConnection::makeConnectionByUrl($dbUri, $this->logger);
        $this->ydb = $connect->getYdb();

        return $connect;
    }

    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    private static function overrideBaseTypes(): void
    {
        Type::overrideType(Types::DATETIME_MUTABLE, DateTimeType::class);
        Type::overrideType(Types::DATETIMETZ_MUTABLE, DateTimeTzType::class);
        Type::overrideType(Types::FLOAT, FloatType::class);
        Type::overrideType(Types::JSON, JsonType::class);
        Type::overrideType(Types::DECIMAL, DecimalType::class);
    }

    public function getDatabasePlatform(): AbstractPlatform
    {
        return new YdbPlatform();
    }

    public function getSchemaManager(Connection $conn, AbstractPlatform $platform): AbstractSchemaManager
    {
        return new YdbSchemaManager($conn, $platform, $this->ydb);
    }

    public function getExceptionConverter(): ExceptionConverter
    {
        // TODO: Implement getExceptionConverter() method.
    }
}
