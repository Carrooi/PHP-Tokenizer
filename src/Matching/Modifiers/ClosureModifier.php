<?php

namespace Carrooi\Tokenizer\Matching\Modifiers;

use Carrooi\Tokenizer\Parsing\Lexer;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class ClosureModifier extends AbstractModifier
{


	/** @var callable */
	private $fn;


	/**
	 * @param callable $fn
	 */
	public function __construct(callable $fn)
	{
		$this->fn = $fn;
	}


	/**
	 * @param \Carrooi\Tokenizer\Parsing\Lexer $lexer
	 * @return int
	 */
	function match(Lexer $lexer)
	{
		return call_user_func($this->fn, $lexer);
	}

}
