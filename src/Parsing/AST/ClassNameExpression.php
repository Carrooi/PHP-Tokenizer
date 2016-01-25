<?php

namespace Carrooi\Tokenizer\Parsing\AST;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class ClassNameExpression extends Entity
{


	/** @var string */
	public $value;


	/**
	 * @param array $tokens
	 * @param string $value
	 */
	public function __construct(array $tokens, $value)
	{
		parent::__construct($tokens);

		$this->value = $value;
	}

}
