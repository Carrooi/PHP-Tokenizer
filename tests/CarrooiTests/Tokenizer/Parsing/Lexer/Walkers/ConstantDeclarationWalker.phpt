<?php

/**
 * Test: Carrooi\Tokenizer\Parsing\Walkers
 *
 * @testCase CarrooiTests\Tokenizer\Parsing\Lexer\Walkers\ConstantDeclarationWalkerTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Parsing\Lexer\Walkers;

use Carrooi\Tokenizer\Parsing\AST\ConstantDeclaration;
use Carrooi\Tokenizer\Parsing\Lexer;
use Carrooi\Tokenizer\Tokenizer;
use CarrooiTests\Tokenizer\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../../../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class ConstantDeclarationWalkerTest extends TestCase
{


	public function testWalkConstant_notFound()
	{
		$tokens = Tokenizer::tokenize('<?php ');
		$lexer = new Lexer($tokens);

		$constant = $lexer->walkConstant();

		Assert::null($constant);
	}


	public function testWalkConstant()
	{
		$tokens = Tokenizer::tokenize('<?php const TEST = 1');
		$lexer = new Lexer($tokens);

		$constant = $lexer->walkConstant();

		Assert::type(ConstantDeclaration::class, $constant);
		Assert::same('TEST', $constant->name);
		Assert::equal([
			$this->createToken('const', 7),
			$this->createToken(' ', 12),
			$this->createToken('TEST', 13),
		], $constant->tokens);
	}

}

run(new ConstantDeclarationWalkerTest);
