<?php

/**
 * Test: Carrooi\Tokenizer\Parsing\Lexer
 *
 * @testCase CarrooiTests\Tokenizer\Parsing\Lexer\Lexer_PeekTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Parsing\Lexer;

use Carrooi\Tokenizer\Parsing\Lexer;
use Carrooi\Tokenizer\Tokenizer;
use CarrooiTests\Tokenizer\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class Lexer_PeekTest extends TestCase
{


	public function testPeek()
	{
		$tokens = Tokenizer::tokenize('<?php if (1) return true;');
		$lexer = new Lexer($tokens);

		$openTag = $this->createToken('<?php', 1);

		Assert::equal($this->createToken(' ', 6), $lexer->peek());

		Assert::equal($openTag, $lexer->token);

		Assert::equal($this->createToken('if', 7), $lexer->peek());

		Assert::equal($openTag, $lexer->token);
	}


	public function testGlimpse()
	{
		$tokens = Tokenizer::tokenize('<?php if (1) return true;');
		$lexer = new Lexer($tokens);

		$space = $this->createToken(' ', 6);

		Assert::equal($space, $lexer->glimpse());
		Assert::equal($space, $lexer->glimpse());

		$lexer->peek();

		$if = $this->createToken('if', 7);

		Assert::equal($if, $lexer->glimpse());
		Assert::equal($if, $lexer->peek());

		Assert::equal($this->createToken('<?php', 1), $lexer->token);
	}


	public function testResetPeek()
	{
		$tokens = Tokenizer::tokenize('<?php if (1) return true;');
		$lexer = new Lexer($tokens);

		$lexer->peek();
		$lexer->peek();
		$lexer->peek();

		Assert::equal($this->createToken('(', 10), $lexer->peek());

		$lexer->resetPeek();

		Assert::equal($this->createToken(' ', 6), $lexer->peek());
	}

}

run(new Lexer_PeekTest());
