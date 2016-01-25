<?php

/**
 * Test: Carrooi\Tokenizer\Matching\Modifiers\AnyOfModifier
 *
 * @testCase CarrooiTests\Tokenizer\Matching\Modifiers\AnyOfModifierTest
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
class AnyOfModifierTest extends TestCase
{


	public function testMatchSimple()
	{
		$tokens = Tokenizer::tokenize('<?php namespace Test\\Test2\\Test3;');

		$matcher = new Matcher;
		$matcher->select(
			$matcher->expr()->anyOf(Lexer::T_STRING, Lexer::T_NS_SEPARATOR)
		);

		$match = $matcher->match($tokens);

		Assert::equal([
			$this->token('Test',  Lexer::T_STRING,       17),
			$this->token('\\',    Lexer::T_NS_SEPARATOR, 21),
			$this->token('Test2', Lexer::T_STRING,       22),
			$this->token('\\',    Lexer::T_NS_SEPARATOR, 27),
			$this->token('Test3', Lexer::T_STRING,       28),
		], $match);
	}

}

run(new AnyOfModifierTest);
