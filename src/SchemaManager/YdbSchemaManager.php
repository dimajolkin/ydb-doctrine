<?php

namespace Dimajolkin\YdbDoctrine\SchemaManager;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;

class YdbSchemaManager extends AbstractSchemaManager
{
    protected function _getPortableTableColumnDefinition($tableColumn)
    {
        // TODO: Implement _getPortableTableColumnDefinition() method.
    }

}
