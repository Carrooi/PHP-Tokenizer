<?php

/**
 * Test: Carrooi\Tokenizer\Matching\MatchBuilder
 *
 * @testCase CarrooiTests\Tokenizer\Matching\Matcher_MatchAllTest
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
class Matcher_MatchAllTest extends TestCase
{


	public function testMatchAll()
	{
		$tokens = Tokenizer::tokenize('<?php true; false; true; false true');

		$matcher = new Matcher;
		$matcher->select(Lexer::T_TRUE, Lexer::T_SEMICOLON);

		$match = $matcher->matchAll($tokens);

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

run(new Matcher_MatchAllTest);
