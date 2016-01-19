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
			$this->createToken('<?php', 1, 1, true),
			$this->createToken(' ', 6, 1, true),
		], $tokens);

		Assert::same('T_OPEN_TAG', $tokens[0]['literal']);
		Assert::same('T_WHITESPACE', $tokens[1]['literal']);
	}


	public function testTokenize_openTag()
	{
		$tokens = Tokenizer::tokenize('<?PHP');

		Assert::equal([
			$this->createToken('<?PHP', 1)
		], $tokens);

		Assert::same(Lexer::T_OPEN_TAG, $tokens[0]['type']);
	}


	public function testTokenize_openTag_short()
	{
		$tokens = Tokenizer::tokenize('<?');

		Assert::equal([
			$this->createToken('<?', 1),
		], $tokens);

		Assert::same(Lexer::T_OPEN_TAG, $tokens[0]['type']);
	}

}

run(new TokenizerTest());
