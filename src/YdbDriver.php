<?php

namespace Dimajolkin\YdbDoctrine;

use Dimajolkin\YdbDoctrine\Parser\YdbUriParser;
use Dimajolkin\YdbDoctrine\SchemaManager\YdbSchemaManager;
use Dimajolkin\YdbDoctrine\Type\DateTimeType;
use Dimajolkin\YdbDoctrine\Type\DateTimeTzType;
use Dimajolkin\YdbDoctrine\Type\FloatType;
use Dimajolkin\YdbDoctrine\Type\JsonType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\API\ExceptionConverter;
use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use YandexCloud\Ydb\Ydb;

class YdbDriver implements Driver
{
    private ?Ydb $ydb = null;

    public function connect(array $params): DriverConnection
    {
        $dbUri = $params['driverOptions']['YBD_URL'] ?? throw new \Exception();
        $this->overrideBaseTypes();
        $connect = YdbConnection::makeConnectionByUrl($dbUri);
        $this->ydb = $connect->getYdb();

        return $connect;
    }

    private static function overrideBaseTypes(): void
    {
        Type::overrideType(Types::DATETIME_MUTABLE, new DateTimeType());
        Type::overrideType(Types::DATETIMETZ_MUTABLE, new DateTimeTzType());
        Type::overrideType(Types::FLOAT, new FloatType());
        Type::overrideType(Types::JSON, new JsonType());
    }

    public function getDatabasePlatform()
    {
        return new YdbPlatform();
    }

    public function getSchemaManager(Connection $conn, AbstractPlatform $platform)
    {
        return new YdbSchemaManager($conn, $platform, $this->ydb);
    }

    public function getExceptionConverter(): ExceptionConverter
    {
        // TODO: Implement getExceptionConverter() method.
    }
}
