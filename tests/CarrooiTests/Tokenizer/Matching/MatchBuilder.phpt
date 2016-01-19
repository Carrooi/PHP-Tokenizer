<?php

/**
 * Test: Carrooi\Tokenizer\Matching\MatchBuilder
 *
 * @testCase CarrooiTests\Tokenizer\Matching\MatchBuilderTest
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
class MatchBuilderTest extends TestCase
{


	public function testSelect()
	{
		$mb = new MatchBuilder;
		$mb
			->select(Lexer::T_LNUMBER, Lexer::T_WHITESPACE)
			->addSelect(Lexer::T_IS_EQUAL)
			->select(Lexer::T_LNUMBER, Lexer::T_WHITESPACE)
			->addSelect(Lexer::T_IS_EQUAL)
			->addSelect(Lexer::T_WHITESPACE)
			->addSelect(Lexer::T_TRUE);

		$selects = $mb->getSelects();

		Assert::equal([
			Lexer::T_LNUMBER,
			Lexer::T_WHITESPACE,
			Lexer::T_IS_EQUAL,
			Lexer::T_WHITESPACE,
			Lexer::T_TRUE,
		], $selects);
	}


	public function testMatchSimple()
	{
		$tokens = Tokenizer::tokenize('<?php true');

		$mb = new MatchBuilder;
		$mb->select(Lexer::T_TRUE);

		$match = $mb->match($tokens);

		Assert::equal([
			$this->token('true', Lexer::T_TRUE, 7, 1),
		], $match);
	}


	public function testMatchAdvanced()
	{
		$tokens = Tokenizer::tokenize('<?php if (1 == true) {}');

		$mb = new MatchBuilder;
		$mb
			->select(Lexer::T_LNUMBER, Lexer::T_WHITESPACE)
			->addSelect(Lexer::T_IS_EQUAL)
			->select(Lexer::T_LNUMBER, Lexer::T_WHITESPACE)
			->addSelect(Lexer::T_IS_EQUAL)
			->addSelect(Lexer::T_WHITESPACE)
			->addSelect(Lexer::T_TRUE);

		$match = $mb->match($tokens);

		Assert::equal([
			$this->token('1',    Lexer::T_LNUMBER,    11),
			$this->token(' ',    Lexer::T_WHITESPACE, 12),
			$this->token('==',   Lexer::T_IS_EQUAL,   13),
			$this->token(' ',    Lexer::T_WHITESPACE, 15),
			$this->token('true', Lexer::T_TRUE,       16),
		], $match);
	}


	public function testMatchNotFirstOccurrence()
	{
		$tokens = Tokenizer::tokenize('<?php true === 1; true == 1');

		$mb = new MatchBuilder;
		$mb->select(Lexer::T_TRUE, Lexer::T_WHITESPACE, Lexer::T_IS_EQUAL, Lexer::T_WHITESPACE, Lexer::T_LNUMBER);

		$match = $mb->match($tokens);

		Assert::equal([
			$this->token('true', Lexer::T_TRUE,       19),
			$this->token(' ',    Lexer::T_WHITESPACE, 23),
			$this->token('==',   Lexer::T_IS_EQUAL,   24),
			$this->token(' ',    Lexer::T_WHITESPACE, 26),
			$this->token('1',    Lexer::T_LNUMBER,    27),
		], $match);
	}


	public function testMatchAll()
	{
		$tokens = Tokenizer::tokenize('<?php true; false; true; false true');

		$mb = new MatchBuilder;
		$mb->select(Lexer::T_TRUE, Lexer::T_SEMICOLON);

		$match = $mb->matchAll($tokens);

		Assert::equal([
			[
				$this->token('true', Lexer::T_TRUE,      7),
				$this->token(';',    Lexer::T_SEMICOLON, 11),
			],
			[
				$this->token('true', Lexer::T_TRUE,      20),
				$this->token(';',    Lexer::T_SEMICOLON, 24),
			],
		], $match);
	}

}

run(new MatchBuilderTest);
