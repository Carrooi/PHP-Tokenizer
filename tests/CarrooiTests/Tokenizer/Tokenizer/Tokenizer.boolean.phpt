<?php

/**
 * Test: Carrooi\Tokenizer\Tokenizer
 *
 * @testCase CarrooiTests\Tokenizer\Tokenizer\Tokenizer_BooleanTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Tokenizer;

use Carrooi\Tokenizer\Parsing\Lexer;
use Carrooi\Tokenizer\Tokenizer;
use CarrooiTests\Tokenizer\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class Tokenizer_BooleanTest extends TestCase
{


	public function testTokenize_boolean_true()
	{
		$tokens = Tokenizer::tokenize('<?php TRUE');

		Assert::equal([
			$this->createToken('<?php', 1),
			$this->createToken(' ', 6),
			$this->createToken('TRUE', 7),
		], $tokens);

		Assert::same(Lexer::T_TRUE, $tokens[2]['type']);
	}


	public function testTokenize_boolean_false()
	{
		$tokens = Tokenizer::tokenize('<?php FALSE');

		Assert::equal([
			$this->createToken('<?php', 1),
			$this->createToken(' ', 6),
			$this->createToken('FALSE', 7),
		], $tokens);

		Assert::same(Lexer::T_FALSE, $tokens[2]['type']);
	}

}

run(new Tokenizer_BooleanTest());
