<?php

namespace Carrooi\Tokenizer\Matching\Modifiers;

use Carrooi\Tokenizer\Matching\Helpers;
use Carrooi\Tokenizer\Matching\Matcher;
use Carrooi\Tokenizer\Parsing\Lexer;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class AnyOfModifier extends AbstractModifier
{


	/** @var \Carrooi\Tokenizer\Matching\Matcher */
	private $builder;

	/** @var array */
	private $tokens;


	/**
	 * @param \Carrooi\Tokenizer\Matching\Matcher $builder
	 * @param array $tokens
	 */
	public function __construct(Matcher $builder, array $tokens)
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
		$tokens = [];

		foreach ($this->tokens as $token) {
			if ($token instanceof AbstractModifier || $token instanceof Matcher) {
				$token = $this->builder->_matchToken($lexer, $token);

				if (is_array($token) && Helpers::isListOfValidTokens($token)) {
					return $token;

				} elseif ($token !== false) {
					$tokens[] = $token;

				}
			} else {
				$tokens[] = $token;
			}
		}

		if (!$lexer->isNextToken($tokens)) {
			return false;
		}

		$result = [];

		while (true) {
			$peek = $lexer->peek();

			if (!$peek) {
				break;
			}

			if (Helpers::isTokenA($peek['type'], $tokens)) {
				$result[] = $peek;

			} else {
				$lexer->resetPeek();
				break;
			}
		}

		return count($result) ? $result : false;
	}

}
