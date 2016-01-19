<?php

/**
 * Test: Carrooi\Tokenizer\Parsing\Walkers
 *
 * @testCase CarrooiTests\Tokenizer\Parsing\Lexer\Walkers\ClassDeclarationWalkerTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Parsing\Lexer\Walkers;

use Carrooi\Tokenizer\Parsing\AST\ClassDeclaration;
use Carrooi\Tokenizer\Parsing\Lexer;
use Carrooi\Tokenizer\Tokenizer;
use CarrooiTests\Tokenizer\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../../../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class ClassDeclarationWalkerTest extends TestCase
{


	public function testWalkClass()
	{
		$tokens = Tokenizer::tokenize('<?php class Test');
		$lexer = new Lexer($tokens);

		$class = $lexer->walkClassDeclaration();

		Assert::type(ClassDeclaration::class, $class);
		Assert::same('Test', $class->name);
		Assert::false($class->abstract);
		Assert::false($class->final);
		Assert::null($class->extends);
		Assert::count(0, $class->implements);
	}


	public function testWalkClass_final()
	{
		$tokens = Tokenizer::tokenize('<?php final class Test');
		$lexer = new Lexer($tokens);

		$class = $lexer->walkClassDeclaration();

		Assert::type(ClassDeclaration::class, $class);
		Assert::same('Test', $class->name);
		Assert::false($class->abstract);
		Assert::true($class->final);
		Assert::null($class->extends);
		Assert::count(0, $class->implements);
	}


	public function testWalkClass_abstract()
	{
		$tokens = Tokenizer::tokenize('<?php abstract class Test');
		$lexer = new Lexer($tokens);

		$class = $lexer->walkClassDeclaration();

		Assert::type(ClassDeclaration::class, $class);
		Assert::same('Test', $class->name);
		Assert::true($class->abstract);
		Assert::false($class->final);
		Assert::null($class->extends);
		Assert::count(0, $class->implements);
	}


	public function testWalkClass_extends()
	{
		$tokens = Tokenizer::tokenize('<?php class Test extends App\BaseTest');
		$lexer = new Lexer($tokens);

		$class = $lexer->walkClassDeclaration();

		Assert::type(ClassDeclaration::class, $class);
		Assert::same('Test', $class->name);
		Assert::false($class->abstract);
		Assert::false($class->final);
		Assert::same('App\BaseTest', $class->extends);
		Assert::count(0, $class->implements);
	}


	public function testWalkClass_implements()
	{
		$tokens = Tokenizer::tokenize('<?php class Test implements ITest, App\ITest');
		$lexer = new Lexer($tokens);

		$class = $lexer->walkClassDeclaration();

		Assert::type(ClassDeclaration::class, $class);
		Assert::same('Test', $class->name);
		Assert::false($class->abstract);
		Assert::false($class->final);
		Assert::null($class->extends);
		Assert::equal([
			'ITest',
			'App\ITest',
		], $class->implements);
	}

}

run(new ClassDeclarationWalkerTest);
