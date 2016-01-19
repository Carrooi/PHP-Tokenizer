<?php

/**
 * Test: Carrooi\Tokenizer\Tokenizer
 *
 * @testCase CarrooiTests\Tokenizer\Tokenizer\Tokenizer_ArrayTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Tokenizer;

use Carrooi\Tokenizer\Parsing\Lexer;
use Carrooi\Tokenizer\Tokenizer;
use CarrooiTests\Tokenizer\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class Tokenizer_ArrayTest extends TestCase
{


	public function testTokenize_array()
	{
		$code = <<<PHP
<?php

[
	'first' => 'First element',
	'second' => 'Second element',
];

PHP;

		$tokens = Tokenizer::tokenize($code);

		Assert::equal([
			$this->token('<?php',            Lexer::T_OPEN_TAG,                 1),
			$this->token("\n\n",             Lexer::T_NEW_LINE,                 6),
			$this->token('[',                Lexer::T_SQUARE_BRACKET_OPEN,      8,  3),
			$this->token("\n",               Lexer::T_NEW_LINE,                 9,  3),
			$this->token("\t",               Lexer::T_TAB,                      10, 4),
			$this->token("'first'",          Lexer::T_CONSTANT_ENCAPSED_STRING, 11, 4),
			$this->token(' ',                Lexer::T_WHITESPACE,               18, 4),
			$this->token('=>',               Lexer::T_DOUBLE_ARROW,             19, 4),
			$this->token(' ',                Lexer::T_WHITESPACE,               21, 4),
			$this->token("'First element'",  Lexer::T_CONSTANT_ENCAPSED_STRING, 22, 4),
			$this->token(',',                Lexer::T_COMMA,                    37, 4),
			$this->token("\n",               Lexer::T_NEW_LINE,                 38, 4),
			$this->token("\t",               Lexer::T_TAB,                      39, 5),
			$this->token("'second'",         Lexer::T_CONSTANT_ENCAPSED_STRING, 40, 5),
			$this->token(' ',                Lexer::T_WHITESPACE,               48, 5),
			$this->token('=>',               Lexer::T_DOUBLE_ARROW,             49, 5),
			$this->token(' ',                Lexer::T_WHITESPACE,               51, 5),
			$this->token("'Second element'", Lexer::T_CONSTANT_ENCAPSED_STRING, 52, 5),
			$this->token(',',                Lexer::T_COMMA,                    68, 5),
			$this->token("\n",               Lexer::T_NEW_LINE,                 69, 5),
			$this->token(']',                Lexer::T_SQUARE_BRACKET_CLOSE,     70, 6),
			$this->token(';',                Lexer::T_SEMICOLON,                71, 6),
			$this->token("\n",               Lexer::T_NEW_LINE,                 72, 6),
		], $tokens);
	}

}

run(new Tokenizer_ArrayTest());
