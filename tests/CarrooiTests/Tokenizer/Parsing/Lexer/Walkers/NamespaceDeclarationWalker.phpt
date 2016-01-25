<?php

/**
 * Test: Carrooi\Tokenizer\Parsing\Walkers
 *
 * @testCase CarrooiTests\Tokenizer\Parsing\Lexer\Walkers\NamespaceDeclarationWalkerTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Parsing\Lexer\Walkers;

use Carrooi\Tokenizer\Parsing\AST\ClassNameExpression;
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
		Assert::type(ClassNameExpression::class, $namespace->name);
		Assert::same('A', $namespace->name->value);
		Assert::equal([
			$this->token('namespace', Lexer::T_NAMESPACE,  7),
			$this->token(' ',         Lexer::T_WHITESPACE, 16),
			$this->token('A',         Lexer::T_STRING,     17),
		], $namespace->tokens);
	}


	public function testWalkNamespaceDeclaration_long()
	{
		$tokens = Tokenizer::tokenize('<?php namespace A\B\C;');
		$lexer = new Lexer($tokens);

		$namespace = $lexer->walkNamespaceDeclaration();

		Assert::type(NamespaceDeclaration::class, $namespace);
		Assert::type(ClassNameExpression::class, $namespace->name);
		Assert::same('A\B\C', $namespace->name->value);
		Assert::equal([
			$this->token('namespace', Lexer::T_NAMESPACE,    7),
			$this->token(' ',         Lexer::T_WHITESPACE,   16),
			$this->token('A',         Lexer::T_STRING,       17),
			$this->token('\\',        Lexer::T_NS_SEPARATOR, 18),
			$this->token('B',         Lexer::T_STRING,       19),
			$this->token('\\',        Lexer::T_NS_SEPARATOR, 20),
			$this->token('C',         Lexer::T_STRING,       21),
		], $namespace->tokens);
	}


	public function testWalkNamespaceDeclaration_long_fqn()
	{
		$tokens = Tokenizer::tokenize('<?php namespace \A\B\C;');
		$lexer = new Lexer($tokens);

		$lexer->moveNext();

		$namespace = $lexer->walkNamespaceDeclaration();

		Assert::type(NamespaceDeclaration::class, $namespace);
		Assert::type(ClassNameExpression::class, $namespace->name);
		Assert::same('\A\B\C', $namespace->name->value);
		Assert::equal([
			$this->token('namespace', Lexer::T_NAMESPACE,    7),
			$this->token(' ',         Lexer::T_WHITESPACE,   16),
			$this->token('\\',        Lexer::T_NS_SEPARATOR, 17),
			$this->token('A',         Lexer::T_STRING,       18),
			$this->token('\\',        Lexer::T_NS_SEPARATOR, 19),
			$this->token('B',         Lexer::T_STRING,       20),
			$this->token('\\',        Lexer::T_NS_SEPARATOR, 21),
			$this->token('C',         Lexer::T_STRING,       22),
		], $namespace->tokens);
	}

}

run(new NamespaceDeclarationWalkerTest());
