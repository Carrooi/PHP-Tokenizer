<?php

namespace Carrooi\Tokenizer\Parsing\AST;
use Carrooi\Tokenizer\InvalidArgumentException;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class ClassDeclaration extends Entity
{


	/** @var string */
	public $name;

	/** @var bool */
	public $final = false;

	/** @var bool */
	public $abstract = false;

	/** @var \Carrooi\Tokenizer\Parsing\AST\ClassNameExpression|null */
	public $extends = null;

	/** @var \Carrooi\Tokenizer\Parsing\AST\ClassNameExpression[] */
	public $implements = [];


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
