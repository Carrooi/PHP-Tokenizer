<?php

/**
 * Test: Carrooi\Tokenizer\Parsing\Walkers
 *
 * @testCase CarrooiTests\Tokenizer\Parsing\Lexer\Walkers\NewInstanceWalkerTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Parsing\Lexer\Walkers;

use Carrooi\Tokenizer\Parsing\AST\NewInstanceExpression;
use Carrooi\Tokenizer\Parsing\AST\ParenthesisExpression;
use Carrooi\Tokenizer\Parsing\Lexer;
use Carrooi\Tokenizer\Tokenizer;
use CarrooiTests\Tokenizer\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../../../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class NewInstanceWalkerTest extends TestCase
{


	public function testWalkNewClass_notFound()
	{
		$tokens = Tokenizer::tokenize('<?php ');
		$lexer = new Lexer($tokens);

		$class = $lexer->walkNewInstance();

		Assert::null($class);
	}


	public function testWalkNewClass_simple_withoutParenthesis()
	{
		$tokens = Tokenizer::tokenize('<?php new A;');
		$lexer = new Lexer($tokens);

		$class = $lexer->walkNewInstance();

		Assert::type(NewInstanceExpression::class, $class);
		Assert::same('A', $class->name);
		Assert::null($class->parenthesis);

		Assert::equal([
			$this->createToken('new', 7),
			$this->createToken(' ', 10),
			$this->createToken('A', 11),
		], $class->tokens);
	}


	public function testWalkNewClass_simple_namespace()
	{
		$tokens = Tokenizer::tokenize('<?php new A\B\C;');
		$lexer = new Lexer($tokens);

		$class = $lexer->walkNewInstance();

		Assert::type(NewInstanceExpression::class, $class);
		Assert::same('A\B\C', $class->name);
		Assert::null($class->parenthesis);

		Assert::equal([
			$this->createToken('new', 7),
			$this->createToken(' ', 10),
			$this->createToken('A', 11),
			$this->createToken('\\', 12),
			$this->createToken('B', 13),
			$this->createToken('\\', 14),
			$this->createToken('C', 15),
		], $class->tokens);
	}


	public function testWalkNewClass_parenthesis()
	{
		$tokens = Tokenizer::tokenize('<?php new A(1);');
		$lexer = new Lexer($tokens);

		$class = $lexer->walkNewInstance();

		Assert::type(NewInstanceExpression::class, $class);
		Assert::same('A', $class->name);
		Assert::type(ParenthesisExpression::class, $class->parenthesis);
		Assert::same('(1)', $class->parenthesis->value);

		Assert::equal([
			$this->createToken('new', 7),
			$this->createToken(' ', 10),
			$this->createToken('A', 11),
			$this->createToken('(', 12),
			$this->createToken('1', 13),
			$this->createToken(')', 14),
		], $class->tokens);

		Assert::equal([
			$this->createToken('(', 12),
			$this->createToken('1', 13),
			$this->createToken(')', 14),
		], $class->parenthesis->tokens);
	}


	public function testWalkNewClass_parenthesis_space()
	{
		$tokens = Tokenizer::tokenize('<?php new A (1);');
		$lexer = new Lexer($tokens);

		$instance = $lexer->walkNewInstance();

		Assert::type(NewInstanceExpression::class, $instance);
		Assert::same('A', $instance->name);
		Assert::type(ParenthesisExpression::class, $instance->parenthesis);
		Assert::same('(1)', $instance->parenthesis->value);

		Assert::equal([
			$this->createToken('new', 7),
			$this->createToken(' ', 10),
			$this->createToken('A', 11),
			$this->createToken(' ', 12),
			$this->createToken('(', 13),
			$this->createToken('1', 14),
			$this->createToken(')', 15),
		], $instance->tokens);

		Assert::equal([
			$this->createToken('(', 13),
			$this->createToken('1', 14),
			$this->createToken(')', 15),
		], $instance->parenthesis->tokens);
	}

}

run(new NewInstanceWalkerTest());
