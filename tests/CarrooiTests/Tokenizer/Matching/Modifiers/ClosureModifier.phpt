<?php

/**
 * Test: Carrooi\Tokenizer\Matching\Modifiers\ClosureModifier
 *
 * @testCase CarrooiTests\Tokenizer\Matching\Modifiers\ClosureModifierTest
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
class ClosureModifierTest extends TestCase
{


	public function testMatchSimple()
	{
		$tokens = Tokenizer::tokenize('<?php true !== false');

		$mb = new MatchBuilder;
		$mb->select($mb->expr()->closure(function(Lexer $lexer) {
			$result = [];

			$lexer->skipUntil(Lexer::T_IS_NOT_IDENTICAL);
			$result[] = $lexer->lookahead;
			$lexer->moveNext();
			$result[] = $lexer->lookahead;
			$lexer->moveNext();
			$result[] = $lexer->lookahead;

			return $result;
		}));

		$match = $mb->match($tokens);

		Assert::equal([
			$this->createToken('!==', 12),
			$this->createToken(' ', 15),
			$this->createToken('false', 16),
		], $match);
	}

}

run(new ClosureModifierTest);
