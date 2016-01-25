<?php

namespace Carrooi\Tokenizer\Parsing\AST;
use Carrooi\Tokenizer\InvalidArgumentException;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class NewInstanceExpression extends Entity
{


	/** @var \Carrooi\Tokenizer\Parsing\AST\ClassNameExpression|string */
	public $name;

	/** @var \Carrooi\Tokenizer\Parsing\AST\ParenthesisExpression|null */
	public $parenthesis = null;


	/**
	 * @param array $tokens
	 * @param \Carrooi\Tokenizer\Parsing\AST\ClassNameExpression|string $name
	 */
	public function __construct(array $tokens, $name)
	{
		parent::__construct($tokens);

		if (!is_string($name) && !$name instanceof ClassNameExpression) {
			throw new InvalidArgumentException(get_class(). ': name must be string or ClassNameExpression.');
		}

		$this->name = $name;
	}

}
