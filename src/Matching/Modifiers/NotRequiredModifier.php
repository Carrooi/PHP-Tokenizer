<?php

namespace Carrooi\Tokenizer\Matching\Modifiers;

use Carrooi\Tokenizer\Matching\MatchBuilder;
use Carrooi\Tokenizer\Parsing\Lexer;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class NotRequiredModifier extends AbstractModifier
{


	/** @var \Carrooi\Tokenizer\Matching\MatchBuilder */
	private $builder;

	/** @var int|\Carrooi\Tokenizer\Matching\Modifiers\AbstractModifier|\Carrooi\Tokenizer\Matching\MatchBuilder */
	private $token;


	/**
	 * @param \Carrooi\Tokenizer\Matching\MatchBuilder $builder
	 * @param int|\Carrooi\Tokenizer\Matching\Modifiers\AbstractModifier|\Carrooi\Tokenizer\Matching\MatchBuilder $token
	 */
	public function __construct(MatchBuilder $builder, $token)
	{
		$this->builder = $builder;
		$this->token = $token;
	}


	/**
	 * @param \Carrooi\Tokenizer\Parsing\Lexer $lexer
	 * @return bool|array|null
	 */
	function match(Lexer $lexer)
	{
		$token = ($this->token instanceof AbstractModifier) || ($this->token instanceof MatchBuilder) ?
			$this->builder->_matchToken($lexer, $this->token) :
			$this->token;

		if ($token === false) {
			return null;

		} elseif ($token === true) {
			return true;

		} elseif (is_int($token)) {
			return $lexer->isNextToken($token) ? true : null;

		} else {
			return $token;
		}
	}

}
