<?php

/**
 * Test: Carrooi\Tokenizer\Matching\Modifiers\AnyOfModifier
 *
 * @testCase CarrooiTests\Tokenizer\Matching\Modifiers\AnyOfModifierTest
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
class AnyOfModifierTest extends TestCase
{


	public function testMatchSimple()
	{
		$tokens = Tokenizer::tokenize('<?php namespace Test\\Test2\\Test3;');

		$mb = new MatchBuilder;
		$mb->select(
			$mb->expr()->anyOf(Lexer::T_STRING, Lexer::T_NS_SEPARATOR)
		);

		$match = $mb->match($tokens);

		Assert::equal([
			$this->createToken('Test', 17),
			$this->createToken('\\', 21),
			$this->createToken('Test2', 22),
			$this->createToken('\\', 27),
			$this->createToken('Test3', 28),
		], $match);
	}

}

run(new AnyOfModifierTest);
