<?php

namespace Carrooi\Tokenizer\Matching\Modifiers;

use Carrooi\Tokenizer\Matching\Helpers;
use Carrooi\Tokenizer\Matching\MatchBuilder;
use Carrooi\Tokenizer\Parsing\Lexer;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class AnyOfModifier extends AbstractModifier
{


	/** @var \Carrooi\Tokenizer\Matching\MatchBuilder */
	private $builder;

	/** @var array */
	private $tokens;


	/**
	 * @param \Carrooi\Tokenizer\Matching\MatchBuilder $builder
	 * @param array $tokens
	 */
	public function __construct(MatchBuilder $builder, array $tokens)
	{
		$this->builder = $builder;
		$this->tokens = $tokens;
	}


	/**
	 * @param \Carrooi\Tokenizer\Parsing\Lexer $lexer
	 * @return int
	 */
	function match(Lexer $lexer)
	{
		foreach ($this->tokens as &$token) {
			if ($token instanceof AbstractModifier) {
				$token = $this->builder->_matchToken($lexer, $token);
			}
		}

		if (!$lexer->isNextToken($this->tokens)) {
			return false;
		}

		$result = [];

		while (true) {
			$peek = $lexer->peek();

			if (!$peek) {
				break;
			}

			if (Helpers::isTokenA($peek['type'], $this->tokens)) {
				$result[] = $peek;

			} else {
				$lexer->resetPeek();
				break;
			}
		}

		return count($result) ? $result : false;
	}

}
