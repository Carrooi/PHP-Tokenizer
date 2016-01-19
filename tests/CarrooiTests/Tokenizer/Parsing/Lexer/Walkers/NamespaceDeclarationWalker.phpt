<?php

/**
 * Test: Carrooi\Tokenizer\Parsing\Walkers
 *
 * @testCase CarrooiTests\Tokenizer\Parsing\Lexer\Walkers\NamespaceDeclarationWalkerTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Parsing\Lexer\Walkers;

use Carrooi\Tokenizer\Parsing\AST\NamespaceDeclaration;
use Carrooi\Tokenizer\Parsing\Lexer;
use Carrooi\Tokenizer\Tokenizer;
use CarrooiTests\Tokenizer\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../../../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class NamespaceDeclarationWalkerTest extends TestCase
{


	public function testWalkNamespaceDeclaration_notFound()
	{
		$tokens = Tokenizer::tokenize('<?php ');
		$lexer = new Lexer($tokens);

		$namespace = $lexer->walkNamespaceDeclaration();

		Assert::null($namespace);
	}


	public function testWalkNamespaceDeclaration_simple()
	{
		$tokens = Tokenizer::tokenize('<?php namespace A;');
		$lexer = new Lexer($tokens);

		$namespace = $lexer->walkNamespaceDeclaration();

		Assert::type(NamespaceDeclaration::class, $namespace);
		Assert::same('A', $namespace->name);
		Assert::equal([
			$this->createToken('namespace', 7),
			$this->createToken(' ', 16),
			$this->createToken('A', 17),
		], $namespace->tokens);
	}


	public function testWalkNamespaceDeclaration_long()
	{
		$tokens = Tokenizer::tokenize('<?php namespace A\B\C;');
		$lexer = new Lexer($tokens);

		$namespace = $lexer->walkNamespaceDeclaration();

		Assert::type(NamespaceDeclaration::class, $namespace);
		Assert::same('A\B\C', $namespace->name);
		Assert::equal([
			$this->createToken('namespace', 7),
			$this->createToken(' ', 16),
			$this->createToken('A', 17),
			$this->createToken('\\', 18),
			$this->createToken('B', 19),
			$this->createToken('\\', 20),
			$this->createToken('C', 21),
		], $namespace->tokens);
	}


	public function testWalkNamespaceDeclaration_long_fqn()
	{
		$tokens = Tokenizer::tokenize('<?php namespace \A\B\C;');
		$lexer = new Lexer($tokens);

		$lexer->moveNext();

		$namespace = $lexer->walkNamespaceDeclaration();

		Assert::type(NamespaceDeclaration::class, $namespace);
		Assert::same('\A\B\C', $namespace->name);
		Assert::equal([
			$this->createToken('namespace', 7),
			$this->createToken(' ', 16),
			$this->createToken('\\', 17),
			$this->createToken('A', 18),
			$this->createToken('\\', 19),
			$this->createToken('B', 20),
			$this->createToken('\\', 21),
			$this->createToken('C', 22),
		], $namespace->tokens);
	}

}

run(new NamespaceDeclarationWalkerTest());
