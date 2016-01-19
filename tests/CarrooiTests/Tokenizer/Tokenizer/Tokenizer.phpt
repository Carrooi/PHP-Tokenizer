<?php

/**
 * Test: Carrooi\Tokenizer\Tokenizer
 *
 * @testCase CarrooiTests\Tokenizer\Tokenizer\TokenizerTest
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
class TokenizerTest extends TestCase
{


	public function testTokenize_withLiterals()
	{
		$tokens = Tokenizer::tokenize('<?php ', true);

		Assert::equal([
			$this->token('<?php', Lexer::T_OPEN_TAG,   1, 1, 'T_OPEN_TAG'),
			$this->token(' ',     Lexer::T_WHITESPACE, 6, 1, 'T_WHITESPACE'),
		], $tokens);
	}


	public function testTokenize_openTag()
	{
		$tokens = Tokenizer::tokenize('<?PHP');

		Assert::equal([
			$this->token('<?PHP', Lexer::T_OPEN_TAG, 1)
		], $tokens);
	}


	public function testTokenize_openTag_short()
	{
		$tokens = Tokenizer::tokenize('<?');

		Assert::equal([
			$this->token('<?', Lexer::T_OPEN_TAG, 1),
		], $tokens);
	}

}

run(new TokenizerTest());
