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
			$this->token('<?php', Lexer::T_OPEN_TAG,   1),
			$this->token(' ',     Lexer::T_WHITESPACE, 6),
			$this->token('NULL',  Lexer::T_NULL,       7),
		], $tokens);
	}

}

run(new Tokenizer_NullTest());
