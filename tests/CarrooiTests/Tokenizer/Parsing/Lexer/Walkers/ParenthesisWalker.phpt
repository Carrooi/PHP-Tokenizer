<?php

/**
 * Test: Carrooi\Tokenizer\Parsing\Walkers
 *
 * @testCase CarrooiTests\Tokenizer\Parsing\Lexer\Walkers\ParenthesisWalkerTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Parsing\Lexer\Walkers;

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
class ParenthesisWalkerTest extends TestCase
{


	public function testWalkParenthesis_simple()
	{
		$tokens = Tokenizer::tokenize('<?php ()');
		$lexer = new Lexer($tokens);

		$parenthesis = $lexer->walkParenthesis();

		Assert::type(ParenthesisExpression::class, $parenthesis);
		Assert::same('()', $parenthesis->value);
		Assert::equal([
			$this->createToken('(', 7),
			$this->createToken(')', 8),
		], $parenthesis->tokens);
	}


	public function testWalkParenthesis_arguments()
	{
		$tokens = Tokenizer::tokenize('<?php (1,(2),(3,(4)))');
		$lexer = new Lexer($tokens);

		$parenthesis = $lexer->walkParenthesis();

		Assert::type(ParenthesisExpression::class, $parenthesis);
		Assert::same('(1,(2),(3,(4)))', $parenthesis->value);
		Assert::equal([
			$this->createToken('(', 7),
			$this->createToken('1', 8),
			$this->createToken(',', 9),
			$this->createToken('(', 10),
			$this->createToken('2', 11),
			$this->createToken(')', 12),
			$this->createToken(',', 13),
			$this->createToken('(', 14),
			$this->createToken('3', 15),
			$this->createToken(',', 16),
			$this->createToken('(', 17),
			$this->createToken('4', 18),
			$this->createToken(')', 19),
			$this->createToken(')', 20),
			$this->createToken(')', 21),
		], $parenthesis->tokens);
	}

}

run(new ParenthesisWalkerTest());
