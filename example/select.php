<?php

include __DIR__ . '/../vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;

$connectionParams = [
    'driverClass' => \Dimajolkin\YdbDoctrine\Driver\YdbDriver::class,
    'driverOptions' => [
        'YBD_URL' => 'ydb://localhost:2136/local?discovery=false&iam_config[anonymous]=true&iam_config[insecure]=true',
    ],
    'wrapperClass' => \Dimajolkin\YdbDoctrine\YdbConnection::class,
    'serverVersion' => '1.4'
];

$conn = DriverManager::getConnection($connectionParams);

$conn->connect();

$res = $conn->executeQuery("SELECT 2");
dd($res->fetchOne());