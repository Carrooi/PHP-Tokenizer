<?php

namespace Carrooi\Tokenizer\Parsing\AST;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class ConstantDeclaration extends Entity
{


	/** @var string */
	public $name;


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
