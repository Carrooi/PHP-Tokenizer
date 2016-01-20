<?php

/**
 * Test: Carrooi\Tokenizer\Tokenizer
 *
 * @testCase CarrooiTests\Tokenizer\Tokenizer\Tokenizer_UnknownTest
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
class Tokenizer_UnknownTest extends TestCase
{


	public function testTokenizeChar_known()
	{
		$tokens = Tokenizer::tokenize('<?php ;');

		Assert::equal([
			$this->token('<?php', Lexer::T_OPEN_TAG,   1),
			$this->token(' ',     Lexer::T_WHITESPACE, 6),
			$this->token(';',     Lexer::T_SEMICOLON,  7),
		], $tokens);
	}


	public function testTokenizeChar_line_new()
	{
		$tokens = Tokenizer::tokenize("<?php\n;");

		Assert::equal([
			$this->token('<?php', Lexer::T_OPEN_TAG,  1),
			$this->token("\n",    Lexer::T_NEW_LINE,  6),
			$this->token(';',     Lexer::T_SEMICOLON, 7, 2),
		], $tokens);
	}


	public function testTokenizeChar_line_same()
	{
		$tokens = Tokenizer::tokenize("<?php\n ;");

		Assert::equal([
			$this->token('<?php', Lexer::T_OPEN_TAG,   1),
			$this->token("\n",    Lexer::T_NEW_LINE,   6),
			$this->token(' ',     Lexer::T_WHITESPACE, 7, 2),
			$this->token(';',     Lexer::T_SEMICOLON,  8, 2),
		], $tokens);
	}

}

run(new Tokenizer_UnknownTest());
