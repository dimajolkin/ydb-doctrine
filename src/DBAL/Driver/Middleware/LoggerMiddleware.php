<?php

namespace Dimajolkin\YdbDoctrine\DBAL\Driver\Middleware;

use Dimajolkin\YdbDoctrine\YdbDriver;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Middleware;
use Psr\Log\LoggerInterface;

class LoggerMiddleware implements Middleware
{
    public function __construct(
        private LoggerInterface $logger
    ) { }

    public function wrap(Driver $driver): Driver
    {
        if ($driver instanceof YdbDriver) {
            $driver->setLogger($this->logger);
        }
        return $driver;
    }
}
