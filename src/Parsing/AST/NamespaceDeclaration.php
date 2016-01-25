<?php

namespace Carrooi\Tokenizer\Parsing\AST;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class NamespaceDeclaration extends Entity
{


	/** @var \Carrooi\Tokenizer\Parsing\AST\ClassNameExpression */
	public $name;


	/**
	 * @param array $tokens
	 * @param \Carrooi\Tokenizer\Parsing\AST\ClassNameExpression $name
	 */
	public function __construct(array $tokens, ClassNameExpression $name)
	{
		parent::__construct($tokens);

		$this->name = $name;
	}

}
