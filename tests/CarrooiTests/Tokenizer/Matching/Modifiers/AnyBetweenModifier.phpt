<?php

/**
 * Test: Carrooi\Tokenizer\Matching\Modifiers\AnyBetweenBuilder
 *
 * @testCase CarrooiTests\Tokenizer\Matching\Modifiers\AnyBetweenModifierTest
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
class AnyBetweenModifierTest extends TestCase
{


	public function testMatchSimple()
	{
		$tokens = Tokenizer::tokenize('<?php (1,2)');

		$matcher = new Matcher;
		$matcher->select(
			$matcher->expr()->anyBetween(Lexer::T_PARENTHESIS_OPEN, Lexer::T_PARENTHESIS_CLOSE)
		);

		$match = $matcher->match($tokens);

		Assert::equal([
			$this->token('(', Lexer::T_PARENTHESIS_OPEN,  7),
			$this->token('1', Lexer::T_LNUMBER,           8),
			$this->token(',', Lexer::T_COMMA,             9),
			$this->token('2', Lexer::T_LNUMBER,           10),
			$this->token(')', Lexer::T_PARENTHESIS_CLOSE, 11),
		], $match);
	}


	public function testMatchRecursive_simple()
	{
		$tokens = Tokenizer::tokenize('<?php (1(2(3)4)5)');

		$matcher = new Matcher;
		$matcher->select(
			$matcher->expr()->anyBetween(Lexer::T_PARENTHESIS_OPEN, Lexer::T_PARENTHESIS_CLOSE, true)
		);

		$match = $matcher->match($tokens);

		Assert::equal([
			$this->token('(', Lexer::T_PARENTHESIS_OPEN, 7),
			$this->token('1', Lexer::T_LNUMBER,          8),
			[
				$this->token('(', Lexer::T_PARENTHESIS_OPEN, 9),
				$this->token('2', Lexer::T_LNUMBER,          10),
				[
					$this->token('(', Lexer::T_PARENTHESIS_OPEN,  11),
					$this->token('3', Lexer::T_LNUMBER,           12),
					$this->token(')', Lexer::T_PARENTHESIS_CLOSE, 13),
				],
				$this->token('4', Lexer::T_LNUMBER,           14),
				$this->token(')', Lexer::T_PARENTHESIS_CLOSE, 15),
			],
			$this->token('5', Lexer::T_LNUMBER,           16),
			$this->token(')', Lexer::T_PARENTHESIS_CLOSE, 17),
		], $match);
	}


	public function testMatchRecursive()
	{
		$tokens = Tokenizer::tokenize('<?php (1,(2,(),2,2),(3))');

		$matcher = new Matcher;
		$matcher->select(
			$matcher->expr()->anyBetween(Lexer::T_PARENTHESIS_OPEN, Lexer::T_PARENTHESIS_CLOSE, true)
		);

		$match = $matcher->match($tokens);

		Assert::equal([
			$this->token('(', Lexer::T_PARENTHESIS_OPEN, 7),
			$this->token('1', Lexer::T_LNUMBER,          8),
			$this->token(',', Lexer::T_COMMA,            9),
			[
				$this->token('(', Lexer::T_PARENTHESIS_OPEN, 10),
				$this->token('2', Lexer::T_LNUMBER,          11),
				$this->token(',', Lexer::T_COMMA,            12),
				[
					$this->token('(', Lexer::T_PARENTHESIS_OPEN,  13),
					$this->token(')', Lexer::T_PARENTHESIS_CLOSE, 14),
				],
				$this->token(',', Lexer::T_COMMA,             15),
				$this->token('2', Lexer::T_LNUMBER,           16),
				$this->token(',', Lexer::T_COMMA,             17),
				$this->token('2', Lexer::T_LNUMBER,           18),
				$this->token(')', Lexer::T_PARENTHESIS_CLOSE, 19),
			],
			$this->token(',', Lexer::T_COMMA, 20),
			[
				$this->token('(', Lexer::T_PARENTHESIS_OPEN,  21),
				$this->token('3', Lexer::T_LNUMBER,           22),
				$this->token(')', Lexer::T_PARENTHESIS_CLOSE, 23),
			],
			$this->token(')', Lexer::T_PARENTHESIS_CLOSE, 24),
		], $match);
	}

}

run(new AnyBetweenModifierTest);
