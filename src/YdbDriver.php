<?php

namespace Dimajolkin\YdbDoctrine;

use Dimajolkin\YdbDoctrine\Parser\YdbUriParser;
use Dimajolkin\YdbDoctrine\SchemaManager\YdbSchemaManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\API\ExceptionConverter;
use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use YandexCloud\Ydb\Ydb;

class YdbDriver implements Driver
{
    private ?Ydb $ydb = null;
    private ?YdbPlatform $platform = null;

    public function connect(array $params): DriverConnection
    {
        $dbUri = $params['driverOptions']['YBD_URL'] ?? throw new \Exception();
        $config = (new YdbUriParser())->parse($dbUri);
        $this->ydb = new Ydb($config);

        return new YdbConnection($this->ydb);
    }

    public function getDatabasePlatform()
    {
        return $this->platform ?: $this->platform = new YdbPlatform($this->ydb);
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
