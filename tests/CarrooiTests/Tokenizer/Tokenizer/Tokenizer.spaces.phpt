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
			$this->token('<?php', Lexer::T_OPEN_TAG,   1),
			$this->Token(' ',     Lexer::T_WHITESPACE, 6),
		], $tokens);
	}


	public function testTokenize_space_more()
	{
		$tokens = Tokenizer::tokenize('<?php     ');

		Assert::equal([
			$this->token('<?php', Lexer::T_OPEN_TAG,   1),
			$this->token('     ', Lexer::T_WHITESPACE, 6),
		], $tokens);
	}


	public function testTokenize_newLine()
	{
		$tokens = Tokenizer::tokenize("<?php\n");

		Assert::equal([
			$this->token('<?php', Lexer::T_OPEN_TAG, 1),
			$this->token("\n",    Lexer::T_NEW_LINE, 6),
		], $tokens);
	}


	public function testTokenize_newLine_more()
	{
		$tokens = Tokenizer::tokenize("<?php\n\r\n\r");

		Assert::equal([
			$this->token('<?php',    Lexer::T_OPEN_TAG, 1),
			$this->token("\n\r\n\r", Lexer::T_NEW_LINE, 6),
		], $tokens);
	}


	public function testTokenize_newLine_mix()
	{
		$tokens = Tokenizer::tokenize("<?php\n\necho\n\n;");

		Assert::equal([
			$this->token('<?php', Lexer::T_OPEN_TAG,  1),
			$this->token("\n\n",  Lexer::T_NEW_LINE,  6),
			$this->token('echo',  Lexer::T_ECHO,      8,  3),
			$this->token("\n\n",  Lexer::T_NEW_LINE,  12, 3),
			$this->token(';',     Lexer::T_SEMICOLON, 14, 5),
		], $tokens);
	}


	public function testTokenize_tab()
	{
		$tokens = Tokenizer::tokenize("<?php\t");

		Assert::equal([
			$this->token('<?php', Lexer::T_OPEN_TAG, 1),
			$this->token("\t",    Lexer::T_TAB,      6),
		], $tokens);
	}


	public function testTokenize_tab_more()
	{
		$tokens = Tokenizer::tokenize("<?php\t\t\t\t");

		Assert::equal([
			$this->token('<?php',    Lexer::T_OPEN_TAG, 1),
			$this->token("\t\t\t\t", Lexer::T_TAB,      6),
		], $tokens);
	}


	public function testTokenize_mix()
	{
		$tokens = Tokenizer::tokenize("<?php \n\r\t\t \r\n\t \t\t");

		Assert::equal([
			$this->token('<?php', Lexer::T_OPEN_TAG,   1),
			$this->token(' ',     Lexer::T_WHITESPACE, 6),
			$this->token("\n\r",  Lexer::T_NEW_LINE,   7),
			$this->token("\t\t",  Lexer::T_TAB,        9, 3),
			$this->token(' ',     Lexer::T_WHITESPACE, 11, 3),
			$this->token("\r\n",  Lexer::T_NEW_LINE,   12, 3),
			$this->token("\t",    Lexer::T_TAB,        14, 4),
			$this->token(' ',     Lexer::T_WHITESPACE, 15, 4),
			$this->token("\t\t",  Lexer::T_TAB,        16, 4),
		], $tokens);

	}


	public function testTokenize_inlineHtml()
	{
		$tokens = Tokenizer::tokenize("\t\n\n\n  \n\t\t \n\t");

		Assert::equal([
			$this->token("\t",     Lexer::T_TAB,        1),
			$this->token("\n\n\n", Lexer::T_NEW_LINE,   2),
			$this->token('  ',     Lexer::T_WHITESPACE, 5,  4),
			$this->token("\n",     Lexer::T_NEW_LINE,   7,  4),
			$this->token("\t\t",   Lexer::T_TAB,        8,  5),
			$this->token(' ',      Lexer::T_WHITESPACE, 10, 5),
			$this->token("\n",     Lexer::T_NEW_LINE,   11, 5),
			$this->token("\t",     Lexer::T_TAB,        12, 6),
		], $tokens);
	}

}

run(new Tokenizer_SpacesTest());
