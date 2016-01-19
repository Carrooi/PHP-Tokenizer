<?php

/**
 * Test: Carrooi\Tokenizer\Parsing\Walkers
 *
 * @testCase CarrooiTests\Tokenizer\Parsing\Lexer\Walkers\NumberExpressionWalkerTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Parsing\Lexer\Walkers;

use Carrooi\Tokenizer\Parsing\AST\ConstantDeclaration;
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
			$this->createToken(' ', 6),
			$this->createToken('5', 7),
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
			$this->createToken(' ', 6),
			$this->createToken('5.69', 7),
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
			$this->createToken('-', 7),
			null,
			$this->createToken('5', 8),
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
			$this->createToken('-', 7),
			$this->createToken(' ', 8),
			$this->createToken('5', 9),
		], $number->tokens);
	}

}

run(new NumberExpressionWalkerTest);
