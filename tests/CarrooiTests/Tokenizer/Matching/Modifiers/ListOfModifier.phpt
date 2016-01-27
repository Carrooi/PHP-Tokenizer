<?php

/**
 * Test: Carrooi\Tokenizer\Matching\Modifiers\ListOfModifier
 *
 * @testCase CarrooiTests\Tokenizer\Matching\Modifiers\ListOfModifierTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Matching\Modifiers;

use Carrooi\Tokenizer\Matching\Matcher;
use Carrooi\Tokenizer\Parsing\Lexer;
use Carrooi\Tokenizer\Tokenizer;
use CarrooiTests\Tokenizer\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class ListOfModifierTest extends TestCase
{


	public function testMatch_simple()
	{
		$tokens = Tokenizer::tokenize('<?php A,B,C,');

		$matcher = new Matcher;
		$matcher->select(
			$matcher->expr()->listOf(Lexer::T_COMMA, Lexer::T_STRING)
		);

		$match = $matcher->match($tokens);

		Assert::equal([
			$this->token('A', Lexer::T_STRING, 7),
			$this->token(',', Lexer::T_COMMA, 8),
			$this->token('B', Lexer::T_STRING, 9),
			$this->token(',', Lexer::T_COMMA, 10),
			$this->token('C', Lexer::T_STRING, 11),
		], $match);
	}


	public function testMatch_anyOf()
	{
		$tokens = Tokenizer::tokenize('<?php A, B , C, ');

		$delimiterMatcher = new Matcher;
		$delimiterMatcher->select(
			$delimiterMatcher->expr()->notRequired(Lexer::T_WHITESPACE),
			Lexer::T_COMMA,
			$delimiterMatcher->expr()->notRequired(Lexer::T_WHITESPACE)
		);

		$matcher = new Matcher;
		$matcher->select(
			$matcher->expr()->listOf(
				$delimiterMatcher,
				Lexer::T_STRING
			)
		);

		$match = $matcher->match($tokens);

		Assert::equal([
			$this->token('A', Lexer::T_STRING, 7),
			[
				null,
				$this->token(',', Lexer::T_COMMA, 8),
				$this->token(' ', Lexer::T_WHITESPACE, 9),
			],
			$this->token('B', Lexer::T_STRING, 10),
			[
				$this->token(' ', Lexer::T_WHITESPACE, 11),
				$this->token(',', Lexer::T_COMMA, 12),
				$this->token(' ', Lexer::T_WHITESPACE, 13),
			],
			$this->token('C', Lexer::T_STRING, 14),
		], $match);
	}

}

run(new ListOfModifierTest);
