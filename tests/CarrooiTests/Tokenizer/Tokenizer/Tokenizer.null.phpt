<?php

/**
 * Test: Carrooi\Tokenizer\Tokenizer
 *
 * @testCase CarrooiTests\Tokenizer\Tokenizer\Tokenizer_NullTest
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
class Tokenizer_NullTest extends TestCase
{


	public function testTokenize_null()
	{
		$tokens = Tokenizer::tokenize('<?php NULL');

		Assert::equal([
			$this->createToken('<?php', 1),
			$this->createToken(' ', 6),
			$this->createToken('NULL', 7),
		], $tokens);

		Assert::same(Lexer::T_NULL, $tokens[2]['type']);
	}

}

run(new Tokenizer_NullTest());
