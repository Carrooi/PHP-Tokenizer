<?php

/**
 * Test: Carrooi\Tokenizer\Matching\MatchBuilder
 *
 * @testCase CarrooiTests\Tokenizer\Matching\MatcherTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Matching;

use Carrooi\Tokenizer\Matching\Matcher;
use Carrooi\Tokenizer\Parsing\Lexer;
use Carrooi\Tokenizer\Tokenizer;
use CarrooiTests\Tokenizer\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class MatcherTest extends TestCase
{


	public function testSelect()
	{
		$matcher = new Matcher;
		$matcher
			->select(Lexer::T_LNUMBER, Lexer::T_WHITESPACE)
			->addSelect(Lexer::T_IS_EQUAL)
			->select(Lexer::T_LNUMBER, Lexer::T_WHITESPACE)
			->addSelect(Lexer::T_IS_EQUAL)
			->addSelect(Lexer::T_WHITESPACE)
			->addSelect(Lexer::T_TRUE);

		$selects = $matcher->getSelects();

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

		$matcher = new Matcher;
		$matcher->select(Lexer::T_TRUE);

		$match = $matcher->match($tokens);

		Assert::equal([
			$this->token('true', Lexer::T_TRUE, 7, 1),
		], $match);
	}


	public function testMatchAdvanced()
	{
		$tokens = Tokenizer::tokenize('<?php if (1 == true) {}');

		$matcher = new Matcher;
		$matcher
			->select(Lexer::T_LNUMBER, Lexer::T_WHITESPACE)
			->addSelect(Lexer::T_IS_EQUAL)
			->select(Lexer::T_LNUMBER, Lexer::T_WHITESPACE)
			->addSelect(Lexer::T_IS_EQUAL)
			->addSelect(Lexer::T_WHITESPACE)
			->addSelect(Lexer::T_TRUE);

		$match = $matcher->match($tokens);

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

		$matcher = new Matcher;
		$matcher->select(Lexer::T_TRUE, Lexer::T_WHITESPACE, Lexer::T_IS_EQUAL, Lexer::T_WHITESPACE, Lexer::T_LNUMBER);

		$match = $matcher->match($tokens);

		Assert::equal([
			$this->token('true', Lexer::T_TRUE,       19),
			$this->token(' ',    Lexer::T_WHITESPACE, 23),
			$this->token('==',   Lexer::T_IS_EQUAL,   24),
			$this->token(' ',    Lexer::T_WHITESPACE, 26),
			$this->token('1',    Lexer::T_LNUMBER,    27),
		], $match);
	}

}

run(new MatcherTest);
