<?php

/**
 * Test: Carrooi\Tokenizer\Parsing\Walkers
 *
 * @testCase CarrooiTests\Tokenizer\Parsing\Lexer\Walkers\ClassNameWalkerTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Parsing\Lexer\Walkers;

use Carrooi\Tokenizer\Parsing\AST\ClassNameExpression;
use Carrooi\Tokenizer\Parsing\Lexer;
use Carrooi\Tokenizer\Tokenizer;
use CarrooiTests\Tokenizer\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../../../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class ClassNameWalkerTest extends TestCase
{


	public function testWalkClassName_simple()
	{
		$tokens = Tokenizer::tokenize('<?php class A');
		$lexer = new Lexer($tokens);

		$className = $lexer->walkClassName();

		Assert::type(ClassNameExpression::class, $className);
		Assert::same('A', $className->value);

		Assert::equal([
			$this->token('A', Lexer::T_STRING, 13),
		], $className->tokens);
	}


	public function testWalkClassName_namespace()
	{
		$tokens = Tokenizer::tokenize('<?php class A\\B\\C');
		$lexer = new Lexer($tokens);

		$className = $lexer->walkClassName();

		Assert::type(ClassNameExpression::class, $className);
		Assert::same('A\\B\\C', $className->value);

		Assert::equal([
			$this->token('A',  Lexer::T_STRING,       13),
			$this->token('\\', Lexer::T_NS_SEPARATOR, 14),
			$this->token('B',  Lexer::T_STRING,       15),
			$this->token('\\', Lexer::T_NS_SEPARATOR, 16),
			$this->token('C',  Lexer::T_STRING,       17),
		], $className->tokens);
	}

}

run(new ClassNameWalkerTest);
