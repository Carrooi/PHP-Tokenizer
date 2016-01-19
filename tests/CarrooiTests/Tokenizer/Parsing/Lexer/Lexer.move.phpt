<?php

/**
 * Test: Carrooi\Tokenizer\Parsing\Lexer
 *
 * @testCase CarrooiTests\Tokenizer\Parsing\Lexer\Lexer_MoveTest
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
class Lexer_MoveTest extends TestCase
{


	/** @var string */
	private $code = "<?php\nreturn true;";

	/** @var array */
	private $expectedTokens;


	public function setUp()
	{
		$this->expectedTokens = [
			$this->createToken('<?php', 1),
			$this->createToken("\n", 6),
			$this->createToken('return', 7, 2),
			$this->createToken(' ', 13, 2),
			$this->createToken('true', 14, 2),
			$this->createToken(';', 18, 2),
		];
	}


	public function testMoveNext()
	{
		$tokens = Tokenizer::tokenize($this->code);
		$lexer = new Lexer($tokens);

		Assert::equal($this->expectedTokens[0], $lexer->token);
		Assert::equal($this->expectedTokens[1], $lexer->lookahead);

		Assert::true($lexer->moveNext());

		Assert::equal($this->expectedTokens[1], $lexer->token);
		Assert::equal($this->expectedTokens[2], $lexer->lookahead);

		Assert::true($lexer->moveNext());

		Assert::equal($this->expectedTokens[2], $lexer->token);
		Assert::equal($this->expectedTokens[3], $lexer->lookahead);

		Assert::true($lexer->moveNext());

		Assert::equal($this->expectedTokens[3], $lexer->token);
		Assert::equal($this->expectedTokens[4], $lexer->lookahead);

		Assert::true($lexer->moveNext());

		Assert::equal($this->expectedTokens[4], $lexer->token);
		Assert::equal($this->expectedTokens[5], $lexer->lookahead);

		Assert::false($lexer->moveNext());

		Assert::equal($this->expectedTokens[5], $lexer->token);
		Assert::null($lexer->lookahead);
	}


	public function testMoveBack()
	{
		$tokens = Tokenizer::tokenize($this->code);
		$lexer = new Lexer($tokens);

		while ($lexer->lookahead) {
			$lexer->moveNext();
		}

		Assert::equal($this->expectedTokens[5], $lexer->token);
		Assert::null($lexer->lookahead);

		Assert::true($lexer->moveBack());

		Assert::equal($this->expectedTokens[4], $lexer->token);
		Assert::equal($this->expectedTokens[5], $lexer->lookahead);

		Assert::true($lexer->moveBack());

		Assert::equal($this->expectedTokens[3], $lexer->token);
		Assert::equal($this->expectedTokens[4], $lexer->lookahead);

		Assert::true($lexer->moveBack());

		Assert::equal($this->expectedTokens[2], $lexer->token);
		Assert::equal($this->expectedTokens[3], $lexer->lookahead);

		Assert::true($lexer->moveBack());

		Assert::equal($this->expectedTokens[1], $lexer->token);
		Assert::equal($this->expectedTokens[2], $lexer->lookahead);

		Assert::false($lexer->moveBack());

		Assert::equal($this->expectedTokens[0], $lexer->token);
		Assert::equal($this->expectedTokens[1], $lexer->lookahead);
	}


	public function testSkipUntil_simple()
	{
		$tokens = Tokenizer::tokenize($this->code);
		$lexer = new Lexer($tokens);

		Assert::true($lexer->skipUntil(Lexer::T_TRUE));

		Assert::equal($this->expectedTokens[3], $lexer->token);
		Assert::equal($this->expectedTokens[4], $lexer->lookahead);
	}


	public function testSkipUntil_simple_notExists()
	{
		$tokens = Tokenizer::tokenize($this->code);
		$lexer = new Lexer($tokens);

		Assert::false($lexer->skipUntil(Lexer::T_FALSE));

		Assert::equal($this->expectedTokens[5], $lexer->token);
		Assert::null($lexer->lookahead);
	}


	public function testSkipUntil_simple_stopAt()
	{
		$tokens = Tokenizer::tokenize("<?php class Test { const ONE = 1, TWO = 2, THREE = 3; const _ONE = 1, const _TWO = 2; }");
		$lexer = new Lexer($tokens);

		Assert::true($lexer->skipUntil(Lexer::T_CONST));

		$count = 0;

		while ($lexer->skipUntil(Lexer::T_COMMA, Lexer::T_SEMICOLON)) {
			$count++;

			$lexer->moveNext();
		}

		Assert::same(2, $count);

		Assert::true($lexer->skipUntil(Lexer::T_CONST));

		$count = 0;

		while ($lexer->skipUntil(Lexer::T_COMMA, Lexer::T_SEMICOLON)) {
			$count++;

			$lexer->moveNext();
		}

		Assert::same(1, $count);
	}


	public function testSkipUntil_array()
	{
		$tokens = Tokenizer::tokenize($this->code);
		$lexer = new Lexer($tokens);

		Assert::true($lexer->skipUntil([Lexer::T_FALSE, Lexer::T_TRUE]));

		Assert::equal($this->expectedTokens[3], $lexer->token);
		Assert::equal($this->expectedTokens[4], $lexer->lookahead);
	}


	public function testSkipUntil_array_notExists()
	{
		$tokens = Tokenizer::tokenize($this->code);
		$lexer = new Lexer($tokens);

		Assert::false($lexer->skipUntil([Lexer::T_CLOSE_TAG, Lexer::T_ABSTRACT]));

		Assert::equal($this->expectedTokens[5], $lexer->token);
		Assert::null($lexer->lookahead);
	}


	public function testReset()
	{
		$tokens = Tokenizer::tokenize($this->code);
		$lexer = new Lexer($tokens);

		$lexer->skipUntil(Lexer::T_TRUE);
		$lexer->reset();

		Assert::equal($this->expectedTokens[0], $lexer->token);
		Assert::equal($this->expectedTokens[1], $lexer->lookahead);
	}

}

run(new Lexer_MoveTest());
