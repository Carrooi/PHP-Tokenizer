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
			$this->token('new', Lexer::T_NEW,        7),
			$this->token(' ',   Lexer::T_WHITESPACE, 10),
			$this->token('A',   Lexer::T_STRING,     11),
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
			$this->token('new', Lexer::T_NEW,          7),
			$this->token(' ',   Lexer::T_WHITESPACE,   10),
			$this->token('A',   Lexer::T_STRING,       11),
			$this->token('\\',  Lexer::T_NS_SEPARATOR, 12),
			$this->token('B',   Lexer::T_STRING,       13),
			$this->token('\\',  Lexer::T_NS_SEPARATOR, 14),
			$this->token('C',   Lexer::T_STRING,       15),
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
			$this->token('new', Lexer::T_NEW,               7),
			$this->token(' ',   Lexer::T_WHITESPACE,        10),
			$this->token('A',   Lexer::T_STRING,            11),
			$this->token('(',   Lexer::T_PARENTHESIS_OPEN,  12),
			$this->token('1',   Lexer::T_LNUMBER,           13),
			$this->token(')',   Lexer::T_PARENTHESIS_CLOSE, 14),
		], $class->tokens);

		Assert::equal([
			$this->token('(', Lexer::T_PARENTHESIS_OPEN,  12),
			$this->token('1', Lexer::T_LNUMBER,           13),
			$this->token(')', Lexer::T_PARENTHESIS_CLOSE, 14),
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
			$this->token('new', Lexer::T_NEW,               7),
			$this->token(' ',   Lexer::T_WHITESPACE,        10),
			$this->token('A',   Lexer::T_STRING,            11),
			$this->token(' ',   Lexer::T_WHITESPACE,        12),
			$this->token('(',   Lexer::T_PARENTHESIS_OPEN,  13),
			$this->token('1',   Lexer::T_LNUMBER,           14),
			$this->token(')',   Lexer::T_PARENTHESIS_CLOSE, 15),
		], $instance->tokens);

		Assert::equal([
			$this->token('(', Lexer::T_PARENTHESIS_OPEN,  13),
			$this->token('1', Lexer::T_LNUMBER,           14),
			$this->token(')', Lexer::T_PARENTHESIS_CLOSE, 15),
		], $instance->parenthesis->tokens);
	}


	public function testWalkNewClass_variable_simple_withoutParenthesis()
	{
		$tokens = Tokenizer::tokenize('<?php new $class;');
		$lexer = new Lexer($tokens);

		$class = $lexer->walkNewInstance();

		Assert::type(NewInstanceExpression::class, $class);
		Assert::same('$class', $class->name);
		Assert::null($class->parenthesis);

		Assert::equal([
			$this->token('new',    Lexer::T_NEW,        7),
			$this->token(' ',      Lexer::T_WHITESPACE, 10),
			$this->token('$class', Lexer::T_VARIABLE,   11),
		], $class->tokens);
	}


	public function testWalkNewClass_variable_simple_withParenthesis()
	{
		$tokens = Tokenizer::tokenize('<?php new $class (false);');
		$lexer = new Lexer($tokens);

		$class = $lexer->walkNewInstance();

		Assert::type(NewInstanceExpression::class, $class);
		Assert::same('$class', $class->name);
		Assert::type(ParenthesisExpression::class, $class->parenthesis);

		Assert::equal([
			$this->token('new',    Lexer::T_NEW,               7),
			$this->token(' ',      Lexer::T_WHITESPACE,        10),
			$this->token('$class', Lexer::T_VARIABLE,          11),
			$this->token(' ',      Lexer::T_WHITESPACE,        17),
			$this->token('(',      Lexer::T_PARENTHESIS_OPEN,  18),
			$this->token('false',  Lexer::T_FALSE,             19),
			$this->token(')',      Lexer::T_PARENTHESIS_CLOSE, 24),
		], $class->tokens);
	}

}

run(new NewInstanceWalkerTest());
