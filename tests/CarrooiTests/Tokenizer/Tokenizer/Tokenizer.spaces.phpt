<?php

/**
 * Test: Carrooi\Tokenizer\Tokenizer
 *
 * @testCase CarrooiTests\Tokenizer\Tokenizer\Tokenizer_SpacesTest
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
class Tokenizer_SpacesTest extends TestCase
{


	public function testTokenize_space()
	{
		$tokens = Tokenizer::tokenize('<?php ');

		Assert::equal([
			$this->createToken('<?php', 1),
			$this->createToken(' ', 6),
		], $tokens);

		Assert::same(Lexer::T_WHITESPACE, $tokens[1]['type']);
	}


	public function testTokenize_space_more()
	{
		$tokens = Tokenizer::tokenize('<?php     ');

		Assert::equal([
			$this->createToken('<?php', 1),
			$this->createToken('     ', 6),
		], $tokens);

		Assert::same(Lexer::T_WHITESPACE, $tokens[1]['type']);
	}


	public function testTokenize_newLine()
	{
		$tokens = Tokenizer::tokenize("<?php\n");

		Assert::equal([
			$this->createToken('<?php', 1),
			$this->createToken("\n", 6),
		], $tokens);

		Assert::same(Lexer::T_NEW_LINE, $tokens[1]['type']);
	}


	public function testTokenize_newLine_more()
	{
		$tokens = Tokenizer::tokenize("<?php\n\r\n\r");

		Assert::equal([
			$this->createToken('<?php', 1),
			$this->createToken("\n\r\n\r", 6),
		], $tokens);

		Assert::same(Lexer::T_NEW_LINE, $tokens[1]['type']);
	}


	public function testTokenize_newLine_mix()
	{
		$tokens = Tokenizer::tokenize("<?php\n\necho\n\n;");

		Assert::equal([
			$this->createToken('<?php', 1),
			$this->createToken("\n\n", 6),
			$this->createToken('echo', 8, 3),
			$this->createToken("\n\n", 12, 3),
			$this->createToken(';', 14, 5),
		], $tokens);

		Assert::same(Lexer::T_NEW_LINE, $tokens[1]['type']);
		Assert::same(Lexer::T_NEW_LINE, $tokens[3]['type']);
	}


	public function testTokenize_tab()
	{
		$tokens = Tokenizer::tokenize("<?php\t");

		Assert::equal([
			$this->createToken('<?php', 1),
			$this->createToken("\t", 6),
		], $tokens);

		Assert::same(Lexer::T_TAB, $tokens[1]['type']);
	}


	public function testTokenize_tab_more()
	{
		$tokens = Tokenizer::tokenize("<?php\t\t\t\t");

		Assert::equal([
			$this->createToken('<?php', 1),
			$this->createToken("\t\t\t\t", 6),
		], $tokens);

		Assert::same(Lexer::T_TAB, $tokens[1]['type']);
	}


	public function testTokenize_mix()
	{
		$tokens = Tokenizer::tokenize("<?php \n\r\t\t \r\n\t \t\t");

		Assert::equal([
			$this->createToken('<?php', 1),
			$this->createToken(' ', 6),
			$this->createToken("\n\r", 7),
			$this->createToken("\t\t", 9, 3),
			$this->createToken(' ', 11, 3),
			$this->createToken("\r\n", 12, 3),
			$this->createToken("\t", 14, 4),
			$this->createToken(' ', 15, 4),
			$this->createToken("\t\t", 16, 4),
		], $tokens);

		Assert::same(Lexer::T_WHITESPACE, $tokens[1]['type']);
		Assert::same(Lexer::T_NEW_LINE, $tokens[2]['type']);
		Assert::same(Lexer::T_TAB, $tokens[3]['type']);
		Assert::same(Lexer::T_WHITESPACE, $tokens[4]['type']);
		Assert::same(Lexer::T_NEW_LINE, $tokens[5]['type']);
		Assert::same(Lexer::T_TAB, $tokens[6]['type']);
		Assert::same(Lexer::T_WHITESPACE, $tokens[7]['type']);
		Assert::same(Lexer::T_TAB, $tokens[8]['type']);

	}


	public function testTokenize_inlineHtml()
	{
		$tokens = Tokenizer::tokenize("\t\n\n\n  \n\t\t \n\t");

		Assert::equal([
			$this->createToken("\t", 1),
			$this->createToken("\n\n\n", 2),
			$this->createToken('  ', 5, 4),
			$this->createToken("\n", 7, 4),
			$this->createToken("\t\t", 8, 5),
			$this->createToken(' ', 10, 5),
			$this->createToken("\n", 11, 5),
			$this->createToken("\t", 12, 6),
		], $tokens);

		Assert::same(Lexer::T_TAB, $tokens[0]['type']);
		Assert::same(Lexer::T_NEW_LINE, $tokens[1]['type']);
		Assert::same(Lexer::T_WHITESPACE, $tokens[2]['type']);
		Assert::same(Lexer::T_NEW_LINE, $tokens[3]['type']);
		Assert::same(Lexer::T_TAB, $tokens[4]['type']);
		Assert::same(Lexer::T_WHITESPACE, $tokens[5]['type']);
		Assert::same(Lexer::T_NEW_LINE, $tokens[6]['type']);
		Assert::same(Lexer::T_TAB, $tokens[7]['type']);
	}

}

run(new Tokenizer_SpacesTest());
