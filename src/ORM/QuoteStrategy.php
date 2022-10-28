<?php

namespace Dimajolkin\YdbDoctrine\ORM;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\DefaultQuoteStrategy;

class QuoteStrategy extends DefaultQuoteStrategy
{
//    public function getColumnAlias($columnName, $counter, AbstractPlatform $platform, ?ClassMetadata $class = null): string
//    {
//        return $columnName;
//    }
}
