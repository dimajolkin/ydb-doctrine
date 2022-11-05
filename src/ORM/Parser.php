<?php

namespace Dimajolkin\YdbDoctrine\ORM;

use Dimajolkin\YdbDoctrine\ORM\Hack\Setter;
use Dimajolkin\YdbDoctrine\ORM\Query\YdbWalker;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\ParserResult;
use \Doctrine\ORM\Query\Parser as DoctrineParser;

class Parser extends DoctrineParser
{
    public function __construct(Query $query)
    {
        $setter = new Setter($this, DoctrineParser::class);
        $setter->setValue('query', $query);
        $setter->setValue('em', $query->getEntityManager());
        $setter->setValue('lexer', new Lexer((string) $query->getDQL()));
        $setter->setValue('parserResult', new ParserResult());

        $this->setCustomOutputTreeWalker(YdbWalker::class);
    }
}
