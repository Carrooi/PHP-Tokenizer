<?php

/**
 * Test: Carrooi\Tokenizer\Parsing\Lexer
 *
 * @testCase CarrooiTests\Tokenizer\Parsing\Lexer\LexerTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Parsing\Lexer;

use Carrooi\Tokenizer\Parsing\Lexer;
use Carrooi\Tokenizer\Tokenizer;
use CarrooiTests\Tokenizer\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class LexerTest extends TestCase
{


	public function testGetLiteral()
	{
		$lexer = new Lexer([]);

		Assert::same('T_DOLLAR', $lexer->getLiteral(Lexer::T_DOLLAR));
	}


	public function testGetLiteral_negative()
	{
		$lexer = new Lexer([]);

		Assert::same('~T_DOLLAR', $lexer->getLiteral(~Lexer::T_DOLLAR));
	}


	public function testCount_empty()
	{
		$lexer = new Lexer([]);

		Assert::same(0, $lexer->count());
	}


	public function testCount()
	{
		$tokens = Tokenizer::tokenize('<?php ');
		$lexer = new Lexer($tokens);

		Assert::same(2, $lexer->count());
	}

}

run(new LexerTest());
