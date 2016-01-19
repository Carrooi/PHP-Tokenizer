<?php

/**
 * Test: Carrooi\Tokenizer\Parsing\Walkers
 *
 * @testCase CarrooiTests\Tokenizer\Parsing\Lexer\Walkers\NumberExpressionWalkerTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Parsing\Lexer\Walkers;

use Carrooi\Tokenizer\Parsing\AST\NumberExpression;
use Carrooi\Tokenizer\Parsing\Lexer;
use Carrooi\Tokenizer\Tokenizer;
use CarrooiTests\Tokenizer\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../../../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class NumberExpressionWalkerTest extends TestCase
{


	public function testMatch_int()
	{
		$tokens = Tokenizer::tokenize('<?php 5');
		$lexer = new Lexer($tokens);

		$number = $lexer->walkNumber();

		Assert::type(NumberExpression::class, $number);
		Assert::same(5, $number->number);
		Assert::true($number->plus);

		Assert::equal([
			null,
			$this->token(' ', Lexer::T_WHITESPACE, 6),
			$this->token('5', Lexer::T_LNUMBER,    7),
		], $number->tokens);
	}


	public function testMatch_float()
	{
		$tokens = Tokenizer::tokenize('<?php 5.69');
		$lexer = new Lexer($tokens);

		$number = $lexer->walkNumber();

		Assert::type(NumberExpression::class, $number);
		Assert::same(5.69, $number->number);
		Assert::true($number->plus);

		Assert::equal([
			null,
			$this->token(' ',    Lexer::T_WHITESPACE, 6),
			$this->token('5.69', Lexer::T_DNUMBER,    7),
		], $number->tokens);
	}


	public function testMatch_minus()
	{
		$tokens = Tokenizer::tokenize('<?php -5');
		$lexer = new Lexer($tokens);

		$number = $lexer->walkNumber();

		Assert::type(NumberExpression::class, $number);
		Assert::same(5, $number->number);
		Assert::false($number->plus);

		Assert::equal([
			$this->token('-', Lexer::T_MINUS, 7),
			null,
			$this->token('5', Lexer::T_LNUMBER, 8),
		], $number->tokens);
	}


	public function testMatch_minus_whitespace()
	{
		$tokens = Tokenizer::tokenize('<?php - 5');
		$lexer = new Lexer($tokens);

		$number = $lexer->walkNumber();

		Assert::type(NumberExpression::class, $number);
		Assert::same(5, $number->number);
		Assert::false($number->plus);

		Assert::equal([
			$this->token('-', Lexer::T_MINUS,      7),
			$this->token(' ', Lexer::T_WHITESPACE, 8),
			$this->token('5', Lexer::T_LNUMBER,    9),
		], $number->tokens);
	}

}

run(new NumberExpressionWalkerTest);
