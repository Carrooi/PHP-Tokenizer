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
			$this->createToken('true', 7),
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
			$this->createToken('1', 11),
			$this->createToken(' ', 12),
			$this->createToken('==', 13),
			$this->createToken(' ', 15),
			$this->createToken('true', 16),
		], $match);
	}


	public function testMatchNotFirstOccurrence()
	{
		$tokens = Tokenizer::tokenize('<?php true === 1; true == 1');

		$mb = new MatchBuilder;
		$mb->select(Lexer::T_TRUE, Lexer::T_WHITESPACE, Lexer::T_IS_EQUAL, Lexer::T_WHITESPACE, Lexer::T_LNUMBER);

		$match = $mb->match($tokens);

		Assert::equal([
			$this->createToken('true', 19),
			$this->createToken(' ', 23),
			$this->createToken('==', 24),
			$this->createToken(' ', 26),
			$this->createToken('1', 27),
		], $match);
	}


	public function testMatchAll()
	{
		$tokens = Tokenizer::tokenize('<?php true; false; true; false true');

		$mb = new MatchBuilder;
		$mb->select(Lexer::T_TRUE, Lexer::T_SEMICOLON);

		$match = $mb->matchAll($tokens);

		Assert::equal([
			[$this->createToken('true', 7), $this->createToken(';', 11)],
			[$this->createToken('true', 20), $this->createToken(';', 24)],
		], $match);
	}

}

run(new MatchBuilderTest);
