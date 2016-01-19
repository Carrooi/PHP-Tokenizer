<?php

/**
 * Test: Carrooi\Tokenizer\Matching\Modifiers\NotRequiredModifier
 *
 * @testCase CarrooiTests\Tokenizer\Matching\Modifiers\NotRequiredModifierTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Matching\Modifiers;

use Carrooi\Tokenizer\Matching\MatchBuilder;
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

		$matcher = new MatchBuilder;
		$matcher->select(
			Lexer::T_LNUMBER,
			Lexer::T_COMMA,
			$matcher->expr()->notRequired(Lexer::T_STRING),
			Lexer::T_COMMA,
			Lexer::T_LNUMBER
		);

		$match = $matcher->match($tokens);

		Assert::equal([
			$this->createToken('1', 7),
			$this->createToken(',', 8),
			$this->createToken('A', 9),
			$this->createToken(',', 10),
			$this->createToken('2', 11),
		], $match);
	}


	public function testMatch_notExists()
	{
		$tokens = Tokenizer::tokenize('<?php 1,,2');

		$matcher = new MatchBuilder;
		$matcher->select(
			Lexer::T_LNUMBER,
			Lexer::T_COMMA,
			$matcher->expr()->notRequired(Lexer::T_STRING),
			Lexer::T_COMMA,
			Lexer::T_LNUMBER
		);

		$match = $matcher->match($tokens);

		Assert::equal([
			$this->createToken('1', 7),
			$this->createToken(',', 8),
			null,
			$this->createToken(',', 9),
			$this->createToken('2', 10),
		], $match);
	}

}

run(new NotRequiredModifierTest);
