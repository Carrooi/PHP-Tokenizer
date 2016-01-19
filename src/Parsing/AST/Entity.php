<?php

namespace Carrooi\Tokenizer\Parsing\AST;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
abstract class Entity
{


	/** @var array */
	public $tokens;


	/**
	 * @param array $tokens
	 */
	public function __construct(array $tokens)
	{
		$this->tokens = $tokens;
	}

}
