<?php

/**
 * Test: Carrooi\Tokenizer\Matching\Modifiers\NotRequiredModifier
 *
 * @testCase CarrooiTests\Tokenizer\Matching\Modifiers\NotRequiredModifierTest
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
class NotRequiredModifierTest extends TestCase
{


	public function testMatch_exists()
	{
		$tokens = Tokenizer::tokenize('<?php 1,A,2');

		$matcher = new Matcher;
		$matcher->select(
			Lexer::T_LNUMBER,
			Lexer::T_COMMA,
			$matcher->expr()->notRequired(Lexer::T_STRING),
			Lexer::T_COMMA,
			Lexer::T_LNUMBER
		);

		$match = $matcher->match($tokens);

		Assert::equal([
			$this->token('1', Lexer::T_LNUMBER, 7),
			$this->token(',', Lexer::T_COMMA,   8),
			$this->token('A', Lexer::T_STRING,  9),
			$this->token(',', Lexer::T_COMMA,   10),
			$this->token('2', Lexer::T_LNUMBER, 11),
		], $match);
	}


	public function testMatch_notExists()
	{
		$tokens = Tokenizer::tokenize('<?php 1,,2');

		$matcher = new Matcher;
		$matcher->select(
			Lexer::T_LNUMBER,
			Lexer::T_COMMA,
			$matcher->expr()->notRequired(Lexer::T_STRING),
			Lexer::T_COMMA,
			Lexer::T_LNUMBER
		);

		$match = $matcher->match($tokens);

		Assert::equal([
			$this->token('1', Lexer::T_LNUMBER, 7),
			$this->token(',', Lexer::T_COMMA,   8),
			null,
			$this->token(',', Lexer::T_COMMA,   9),
			$this->token('2', Lexer::T_LNUMBER, 10),
		], $match);
	}

}

run(new NotRequiredModifierTest);
