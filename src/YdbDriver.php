<?php

namespace Dimajolkin\YdbDoctrine;

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

    public function connect(array $params): DriverConnection
    {
        $config = [
            'database'    => '/ru-central1/b1glsit4vk53kt31km0j/etnivhona5jpno1ks4lm',
            'endpoint'    => 'ydb.serverless.yandexcloud.net:2135',
            'discovery'   => false,
            'iam_config'  => [
                'service_file'   => __DIR__ . '/../user.json',
            ],
        ];

        $this->ydb = new Ydb($config);
        return new YdbConnection($this->ydb);
    }

    public function getDatabasePlatform()
    {
        return new YdbPlatform();
    }

    public function getSchemaManager(Connection $conn, AbstractPlatform $platform)
    {
        return new YdbSchemaManager($conn, $platform);
    }

    public function getExceptionConverter(): ExceptionConverter
    {
        // TODO: Implement getExceptionConverter() method.
    }
}
