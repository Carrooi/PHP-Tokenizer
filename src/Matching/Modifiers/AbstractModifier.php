<?php

namespace Carrooi\Tokenizer\Matching\Modifiers;

use Carrooi\Tokenizer\Parsing\Lexer;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
abstract class AbstractModifier
{


	/**
	 * @param \Carrooi\Tokenizer\Parsing\Lexer $lexer
	 * @return bool|array|null
	 */
	abstract function match(Lexer $lexer);

}
