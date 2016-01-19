<?php

/**
 * Test: Carrooi\Tokenizer\Matching\Helpers
 *
 * @testCase CarrooiTests\Tokenizer\Matching\HelpersTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Matching;

use Carrooi\Tokenizer\Matching\Helpers;
use Carrooi\Tokenizer\Parsing\Lexer;
use CarrooiTests\Tokenizer\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class HelpersTest extends TestCase
{


	public function testIsTokenA()
	{
		Assert::true(Helpers::isTokenA(Lexer::T_OPEN_TAG, Lexer::T_OPEN_TAG));
		Assert::false(Helpers::isTokenA(Lexer::T_OPEN_TAG, Lexer::T_CLOSE_TAG));

		Assert::true(Helpers::isTokenA(Lexer::T_OPEN_TAG, [Lexer::T_CLOSE_TAG, Lexer::T_OPEN_TAG]));
		Assert::false(Helpers::isTokenA(Lexer::T_OPEN_TAG, [Lexer::T_CLOSE_TAG, Lexer::T_BRACES_CLOSE]));

		Assert::true(Helpers::isTokenA(Lexer::T_OPEN_TAG, ~Lexer::T_CLOSE_TAG));
		Assert::false(Helpers::isTokenA(Lexer::T_OPEN_TAG, ~Lexer::T_OPEN_TAG));

		Assert::false(Helpers::isTokenA(Lexer::T_OPEN_TAG, [~Lexer::T_CLOSE_TAG, ~Lexer::T_OPEN_TAG]));
		Assert::true(Helpers::isTokenA(Lexer::T_OPEN_TAG, [~Lexer::T_CLOSE_TAG, ~Lexer::T_BRACES_CLOSE]));
	}

}

run(new HelpersTest);
