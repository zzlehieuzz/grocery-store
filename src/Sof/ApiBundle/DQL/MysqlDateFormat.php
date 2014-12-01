<?php

namespace Sof\ApiBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

class MysqlDateFormat extends FunctionNode
{
    /**
     * Holds the timestamp of the DATE_FORMAT DQL statement
     * @var $dateExpression
     */
    protected $dateExpression;

    /**
     * Holds the '% format' parameter of the DATE_FORMAT DQL statement
     * var String
     */
    protected $formatChar;

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'DATE_FORMAT (' . $sqlWalker->walkArithmeticExpression( $this->dateExpression ) . ',' . $sqlWalker->walkStringPrimary( $this->formatChar ) . ')';
    }

    public function parse(\Doctrine\ORM\Query\Parser $parser )
    {
        $parser->Match( Lexer::T_IDENTIFIER );
        $parser->Match( Lexer::T_OPEN_PARENTHESIS );

        $this->dateExpression = $parser->ArithmeticExpression();
        $parser->Match( Lexer::T_COMMA );

        $this->formatChar = $parser->ArithmeticExpression();

        $parser->Match( Lexer::T_CLOSE_PARENTHESIS );
    }


}
