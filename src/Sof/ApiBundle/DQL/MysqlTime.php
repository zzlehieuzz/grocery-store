<?php

namespace Sof\ApiBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

class MysqlTime extends FunctionNode
{
  public $stringTime;

  public function parse(\Doctrine\ORM\Query\Parser $parser)
  {
    $parser->match(Lexer::T_IDENTIFIER);
    $parser->match(Lexer::T_OPEN_PARENTHESIS);
    $this->stringTime = $parser->StringPrimary();
    $parser->match(Lexer::T_CLOSE_PARENTHESIS);
  }

  public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
  {
    return 'TIME(' . $this->stringTime->dispatch($sqlWalker) . ')';
  }
}
