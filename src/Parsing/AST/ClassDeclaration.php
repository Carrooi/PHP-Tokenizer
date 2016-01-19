<?php

namespace Carrooi\Tokenizer\Parsing\AST;

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

	/** @var string|null */
	public $extends = null;

	/** @var array */
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
