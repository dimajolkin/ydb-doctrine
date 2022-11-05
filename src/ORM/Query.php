<?php

declare(strict_types=1);

namespace Dimajolkin\YdbDoctrine\ORM;

include_once __DIR__ . '/../../doctrine/Query.php';

use Doctrine\ORM\Query as DoctrineQuery;

/**
 * A Query object represents a DQL query.
 */
class Query extends DoctrineQuery
{

}
