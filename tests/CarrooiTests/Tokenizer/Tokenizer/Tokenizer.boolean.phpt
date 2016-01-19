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
			$this->token('<?php', Lexer::T_OPEN_TAG,   1),
			$this->token(' ',     Lexer::T_WHITESPACE, 6),
			$this->token('TRUE',  Lexer::T_TRUE,       7),
		], $tokens);
	}


	public function testTokenize_boolean_false()
	{
		$tokens = Tokenizer::tokenize('<?php FALSE');

		Assert::equal([
			$this->token('<?php', Lexer::T_OPEN_TAG,   1),
			$this->token(' ',     Lexer::T_WHITESPACE, 6),
			$this->token('FALSE', Lexer::T_FALSE,      7),
		], $tokens);
	}

}

run(new Tokenizer_BooleanTest());
