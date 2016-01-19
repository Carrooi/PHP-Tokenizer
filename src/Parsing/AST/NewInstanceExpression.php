<?php

namespace Carrooi\Tokenizer\Parsing\AST;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class NewInstanceExpression extends Entity
{


	/** @var string */
	public $name;

	/** @var \Carrooi\Tokenizer\Parsing\AST\ParenthesisExpression|null */
	public $parenthesis = null;


	/**
	 * @param array $tokens
	 * @param string $name
	 */
	public function __construct(array $tokens, $name)
	{
		parent::__construct($tokens);

		$this->name = $name;
	}

}
