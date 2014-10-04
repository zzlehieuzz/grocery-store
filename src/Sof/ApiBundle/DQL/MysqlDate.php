<?php

namespace Sof\ApiBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

class MysqlDate extends FunctionNode
{
  public $stringDate;

  public function parse(\Doctrine\ORM\Query\Parser $parser)
  {
    $parser->match(Lexer::T_IDENTIFIER);
    $parser->match(Lexer::T_OPEN_PARENTHESIS);
    $this->stringDate = $parser->StringPrimary();
    $parser->match(Lexer::T_CLOSE_PARENTHESIS);
  }

  public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
  {
    return 'DATE(' . $this->stringDate->dispatch($sqlWalker) . ')';
  }
}
