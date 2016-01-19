<?php

/**
 * Test: Carrooi\Tokenizer\Matching\MatchBuilder
 *
 * @testCase CarrooiTests\Tokenizer\Matching\MatchBuilder_SubMatchBuilderTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Matching;

use Carrooi\Tokenizer\Matching\MatchBuilder;
use Carrooi\Tokenizer\Parsing\Lexer;
use Carrooi\Tokenizer\Tokenizer;
use CarrooiTests\Tokenizer\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class MatchBuilder_SubMatchBuilderTest extends TestCase
{


	public function testMatchSubMatchBuilder()
	{
		$tokens = Tokenizer::tokenize('<?php if (true) {}');

		$mb = new MatchBuilder;
		$mb
			->select(Lexer::T_PARENTHESIS_OPEN)
			->addSelect((new MatchBuilder)->select(Lexer::T_TRUE))
			->addSelect(Lexer::T_PARENTHESIS_CLOSE)
		;

		$match = $mb->match($tokens);

		Assert::equal([
			$this->createToken('(', 10),
			[
				$this->createToken('true', 11),
			],
			$this->createToken(')', 15),
		], $match);
	}


	public function testMatchSubMatchBuilder_modifiers()
	{
		$tokens = Tokenizer::tokenize('<?php final class');

		$type = new MatchBuilder;
		$type->select(
			$type->expr()->anyOf(
				Lexer::T_FINAL,
				Lexer::T_ABSTRACT
			),
			Lexer::T_WHITESPACE
		);

		$matcher = new MatchBuilder;
		$matcher->select(
			$matcher->expr()->notRequired($type),
			Lexer::T_CLASS
		);

		$match = $matcher->match($tokens);

		Assert::equal([
			[
				$this->createToken('final', 7),
				$this->createToken(' ', 12),
			],
			$this->createToken('class', 13),
		], $match);
	}

}

run(new MatchBuilder_SubMatchBuilderTest);
