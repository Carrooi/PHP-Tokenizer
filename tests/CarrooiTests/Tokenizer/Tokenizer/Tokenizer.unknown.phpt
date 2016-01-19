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
			$this->createToken('<?php', 1),
			$this->createToken(' ', 6),
			$this->createToken(';', 7),
		], $tokens);

		Assert::same(Lexer::T_SEMICOLON, $tokens[2]['type']);
	}


	public function testTokenizeChar_line_new()
	{
		$tokens = Tokenizer::tokenize("<?php\n;");

		Assert::equal([
			$this->createToken('<?php', 1),
			$this->createToken("\n", 6),
			$this->createToken(';', 7, 2),
		], $tokens);

		Assert::same(Lexer::T_SEMICOLON, $tokens[2]['type']);
	}


	public function testTokenizeChar_line_same()
	{
		$tokens = Tokenizer::tokenize("<?php\n ;");

		Assert::equal([
			$this->createToken('<?php', 1),
			$this->createToken("\n", 6),
			$this->createToken(' ', 7, 2),
			$this->createToken(';', 8, 2),
		], $tokens);

		Assert::same(Lexer::T_SEMICOLON, $tokens[3]['type']);
	}

}

run(new Tokenizer_UnknownTest());
