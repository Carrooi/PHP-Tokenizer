<?php

/**
 * Test: Carrooi\Tokenizer\Matching\Modifiers\AnyBetweenBuilder
 *
 * @testCase CarrooiTests\Tokenizer\Matching\Modifiers\AnyBetweenModifierTest
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
class AnyBetweenModifierTest extends TestCase
{


	public function testMatchSimple()
	{
		$tokens = Tokenizer::tokenize('<?php (1,2)');

		$mb = new MatchBuilder;
		$mb->select(
			$mb->expr()->anyBetween(Lexer::T_PARENTHESIS_OPEN, Lexer::T_PARENTHESIS_CLOSE)
		);

		$match = $mb->match($tokens);

		Assert::equal([
			$this->createToken('(', 7),
			$this->createToken('1', 8),
			$this->createToken(',', 9),
			$this->createToken('2', 10),
			$this->createToken(')', 11),
		], $match);
	}


	public function testMatchRecursive_simple()
	{
		$tokens = Tokenizer::tokenize('<?php (1(2(3)4)5)');

		$mb = new MatchBuilder;
		$mb->select(
			$mb->expr()->anyBetween(Lexer::T_PARENTHESIS_OPEN, Lexer::T_PARENTHESIS_CLOSE, true)
		);

		$match = $mb->match($tokens);

		Assert::equal([
			$this->createToken('(', 7),
			$this->createToken('1', 8),
			[
				$this->createToken('(', 9),
				$this->createToken('2', 10),
				[
					$this->createToken('(', 11),
					$this->createToken('3', 12),
					$this->createToken(')', 13),
				],
				$this->createToken('4', 14),
				$this->createToken(')', 15),
			],
			$this->createToken('5', 16),
			$this->createToken(')', 17),
		], $match);
	}


	public function testMatchRecursive()
	{
		$tokens = Tokenizer::tokenize('<?php (1,(2,(),2,2),(3))');

		$mb = new MatchBuilder;
		$mb->select(
			$mb->expr()->anyBetween(Lexer::T_PARENTHESIS_OPEN, Lexer::T_PARENTHESIS_CLOSE, true)
		);

		$match = $mb->match($tokens);

		Assert::equal([
			$this->createToken('(', 7),
			$this->createToken('1', 8),
			$this->createToken(',', 9),
			[
				$this->createToken('(', 10),
				$this->createToken('2', 11),
				$this->createToken(',', 12),
				[
					$this->createToken('(', 13),
					$this->createToken(')', 14),
				],
				$this->createToken(',', 15),
				$this->createToken('2', 16),
				$this->createToken(',', 17),
				$this->createToken('2', 18),
				$this->createToken(')', 19),
			],
			$this->createToken(',', 20),
			[
				$this->createToken('(', 21),
				$this->createToken('3', 22),
				$this->createToken(')', 23),
			],
			$this->createToken(')', 24),
		], $match);
	}

}

run(new AnyBetweenModifierTest);
