<?php

/**
 * Test: Carrooi\Tokenizer\Matching\Modifiers\ClosureModifier
 *
 * @testCase CarrooiTests\Tokenizer\Matching\Modifiers\ClosureModifierTest
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
class ClosureModifierTest extends TestCase
{


	public function testMatchSimple()
	{
		$tokens = Tokenizer::tokenize('<?php true !== false');

		$matcher = new Matcher;
		$matcher->select($matcher->expr()->closure(function(Lexer $lexer) {
			$result = [];

			$lexer->skipUntil(Lexer::T_IS_NOT_IDENTICAL);
			$result[] = $lexer->lookahead;
			$lexer->moveNext();
			$result[] = $lexer->lookahead;
			$lexer->moveNext();
			$result[] = $lexer->lookahead;

			return $result;
		}));

		$match = $matcher->match($tokens);

		Assert::equal([
			$this->token('!==',   Lexer::T_IS_NOT_IDENTICAL, 12),
			$this->token(' ',     Lexer::T_WHITESPACE,       15),
			$this->token('false', Lexer::T_FALSE,            16),
		], $match);
	}

}

run(new ClosureModifierTest);
