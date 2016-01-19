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
			$this->token('(', Lexer::T_PARENTHESIS_OPEN,  7),
			$this->token(')', Lexer::T_PARENTHESIS_CLOSE, 8),
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
			$this->token('(', Lexer::T_PARENTHESIS_OPEN,  7),
			$this->token('1', Lexer::T_LNUMBER,           8),
			$this->token(',', Lexer::T_COMMA,             9),
			$this->token('(', Lexer::T_PARENTHESIS_OPEN,  10),
			$this->token('2', Lexer::T_LNUMBER,           11),
			$this->token(')', Lexer::T_PARENTHESIS_CLOSE, 12),
			$this->token(',', Lexer::T_COMMA,             13),
			$this->token('(', Lexer::T_PARENTHESIS_OPEN,  14),
			$this->token('3', Lexer::T_LNUMBER,           15),
			$this->token(',', Lexer::T_COMMA,             16),
			$this->token('(', Lexer::T_PARENTHESIS_OPEN,  17),
			$this->token('4', Lexer::T_LNUMBER,           18),
			$this->token(')', Lexer::T_PARENTHESIS_CLOSE, 19),
			$this->token(')', Lexer::T_PARENTHESIS_CLOSE, 20),
			$this->token(')', Lexer::T_PARENTHESIS_CLOSE, 21),
		], $parenthesis->tokens);
	}

}

run(new ParenthesisWalkerTest());
