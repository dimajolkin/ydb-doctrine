<?php

namespace Dimajolkin\YdbDoctrine\ORM\Functions;

use Dimajolkin\YdbDoctrine\ORM\Functions\Expression\RandExpression;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;

class Rand extends FunctionNode
{
    private RandExpression $randExpression;

    private function makeRandExpression(Parser $parser): RandExpression
    {
        $lexer = $parser->getLexer();
        $functionName = $lexer->lookahead['value'];
        if ($functionName !== 'RAND') {
            throw new QueryException();
        }
        $parser->match($lexer->lookahead['type']);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $tableAlias = $lexer->lookahead['value'];
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_DOT);
        $columnName = $lexer->lookahead['value'];
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);

        return new RandExpression($tableAlias, $columnName);
    }

    public function parse(Parser $parser): void
    {
        $this->randExpression = $this->makeRandExpression($parser);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return $this->randExpression->dispatch($sqlWalker);
    }
}
