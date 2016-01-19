<?php

namespace Carrooi\Tokenizer\Parsing\AST;

use Carrooi\Tokenizer\Parsing\Lexer;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class NumberExpression extends Entity
{


	/** @var int|float */
	public $number;

	/** @var bool */
	public $plus = true;


	/**
	 * @param array $tokens
	 * @param int|float $number
	 */
	public function __construct(array $tokens, $number)
	{
		parent::__construct($tokens);

		$this->number = $tokens[2]['type'] === Lexer::T_LNUMBER ?
			(int) $number :
			(float) $number;
	}

}
