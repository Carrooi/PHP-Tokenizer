<?php

/**
 * Test: Carrooi\Tokenizer\Parsing\Lexer
 *
 * @testCase CarrooiTests\Tokenizer\Parsing\Lexer\Lexer_IsTokenTest
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
class Lexer_IsTokenTest extends TestCase
{


	public function testIsCurrentToken_simple()
	{
		$tokens = Tokenizer::tokenize('<?php ');
		$lexer = new Lexer($tokens);

		Assert::true($lexer->isCurrentToken(Lexer::T_OPEN_TAG));
		Assert::false($lexer->isCurrentToken(Lexer::T_CLOSE_TAG));
	}


	public function testIsCurrentToken_simple_not()
	{
		$tokens = Tokenizer::tokenize('<?php ');
		$lexer = new Lexer($tokens);

		Assert::false($lexer->isCurrentToken(~Lexer::T_OPEN_TAG));
		Assert::true($lexer->isCurrentToken(~Lexer::T_CLOSE_TAG));
	}


	public function testIsCurrentToken_array()
	{
		$tokens = Tokenizer::tokenize('<?php ');
		$lexer = new Lexer($tokens);

		Assert::true($lexer->isCurrentToken([Lexer::T_OPEN_TAG, Lexer::T_CLOSE_TAG]));
		Assert::false($lexer->isCurrentToken([Lexer::T_WHITESPACE, Lexer::T_TAB]));
	}


	public function testIsCurrentToken_array_not()
	{
		$tokens = Tokenizer::tokenize('<?php ');
		$lexer = new Lexer($tokens);

		Assert::false($lexer->isCurrentToken([~Lexer::T_OPEN_TAG, ~Lexer::T_CLOSE_TAG]));
		Assert::true($lexer->isCurrentToken([~Lexer::T_WHITESPACE, ~Lexer::T_TAB]));
	}


	public function testIsNextToken_simple()
	{
		$tokens = Tokenizer::tokenize('<?php ');
		$lexer = new Lexer($tokens);

		Assert::true($lexer->isNextToken(Lexer::T_WHITESPACE));
		Assert::false($lexer->isNextToken(Lexer::T_TAB));
	}


	public function testIsNextToken_simple_not()
	{
		$tokens = Tokenizer::tokenize('<?php ');
		$lexer = new Lexer($tokens);

		Assert::false($lexer->isNextToken(~Lexer::T_WHITESPACE));
		Assert::true($lexer->isNextToken(~Lexer::T_TAB));
	}


	public function testIsNextToken_array()
	{
		$tokens = Tokenizer::tokenize('<?php ');
		$lexer = new Lexer($tokens);

		Assert::true($lexer->isNextToken([Lexer::T_TAB, Lexer::T_WHITESPACE]));
		Assert::false($lexer->isNextToken([Lexer::T_OPEN_TAG, Lexer::T_CLOSE_TAG]));
	}


	public function testIsNextToken_array_not()
	{
		$tokens = Tokenizer::tokenize('<?php ');
		$lexer = new Lexer($tokens);

		Assert::false($lexer->isNextToken([~Lexer::T_TAB, ~Lexer::T_WHITESPACE]));
		Assert::true($lexer->isNextToken([~Lexer::T_OPEN_TAG, ~Lexer::T_CLOSE_TAG]));
	}

}

run(new Lexer_IsTokenTest());
