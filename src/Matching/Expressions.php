<?php

namespace Carrooi\Tokenizer\Matching;

use Carrooi\Tokenizer\Matching\Modifiers;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class Expressions
{


	/** @var \Carrooi\Tokenizer\Matching\MatchBuilder */
	private $builder;


	/**
	 * @param \Carrooi\Tokenizer\Matching\MatchBuilder $builder
	 */
	public function __construct(MatchBuilder $builder)
	{
		$this->builder = $builder;
	}


	/**
	 * @param int|\Carrooi\Tokenizer\Matching\Modifiers\AbstractModifier $tokens
	 * @return \Carrooi\Tokenizer\Matching\Modifiers\AnyOfModifier
	 */
	public function anyOf($tokens)
	{
		return new Modifiers\AnyOfModifier($this->builder, is_array($tokens) ? $tokens : func_get_args());
	}


	/**
	 * @param int|\Carrooi\Tokenizer\Matching\Modifiers\AbstractModifier $startToken
	 * @param int|\Carrooi\Tokenizer\Matching\Modifiers\AbstractModifier $endToken
	 * @param bool $recursive
	 * @return \Carrooi\Tokenizer\Matching\Modifiers\AnyBetweenModifier
	 */
	public function anyBetween($startToken, $endToken, $recursive = false)
	{
		return new Modifiers\AnyBetweenModifier($this->builder, $startToken, $endToken, $recursive);
	}


	/**
	 * @param int|\Carrooi\Tokenizer\Matching\Modifiers\AbstractModifier|\Carrooi\Tokenizer\Matching\MatchBuilder $token
	 * @return \Carrooi\Tokenizer\Matching\Modifiers\NotRequiredModifier
	 */
	public function notRequired($token)
	{
		return new Modifiers\NotRequiredModifier($this->builder, $token);
	}


	/**
	 * @param callable $fn
	 * @return \Carrooi\Tokenizer\Matching\Modifiers\ClosureModifier
	 */
	public function closure(callable $fn)
	{
		return new Modifiers\ClosureModifier($fn);
	}

}
