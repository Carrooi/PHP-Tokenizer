<?php

namespace Carrooi\Tokenizer\Parsing;

use Carrooi\Tokenizer\Matching\Helpers;
use Carrooi\Tokenizer\Matching\Matcher;
use Carrooi\Tokenizer\Matching\ResultMapping;
use Carrooi\Tokenizer\Parsing\AST\ClassDeclaration;
use Carrooi\Tokenizer\Parsing\AST\NumberExpression;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class Walkers
{


	/** @var \Carrooi\Tokenizer\Parsing\Lexer */
	private $lexer;


	/**
	 * @param \Carrooi\Tokenizer\Parsing\Lexer $lexer
	 */
	public function __construct(Lexer $lexer)
	{
		$this->lexer = $lexer;
	}


	/**
	 * @return \Carrooi\Tokenizer\Matching\Matcher
	 */
	public function number()
	{
		$matcher = new Matcher;

		$matcher->select(
			$matcher->expr()->notRequired(Lexer::T_MINUS),
			$matcher->expr()->notRequired(Lexer::T_WHITESPACE),
			$matcher->expr()->anyOf(
				Lexer::T_LNUMBER,
				Lexer::T_DNUMBER
			)
		);

		$matcher->map(new ResultMapping(function($tokens) {
			if (!$tokens) {
				return null;
			}

			$number = new NumberExpression($tokens, $tokens[2]['value']);

			if ($tokens[0]) {
				$number->plus = false;
			}

			return $number;
		}));

		return $matcher;
	}


	/**
	 * @return \Carrooi\Tokenizer\Matching\Matcher
	 */
	public function namespaceDeclaration()
	{
		$matcher = new Matcher;

		$matcher->select(
			Lexer::T_NAMESPACE,
			Lexer::T_WHITESPACE,
			$matcher->expr()->anyOf(Lexer::T_STRING, Lexer::T_NS_SEPARATOR)
		);

		$matcher->map(new ResultMapping(function($namespace) {
			if (!$namespace) {
				return null;
			}

			$name = array_map(function(array $name) {
				return $name['value'];
			}, array_slice($namespace, 2));
			$name = implode('', $name);

			return new AST\NamespaceDeclaration($namespace, $name);
		}));

		return $matcher;
	}


	/**
	 * @return \Carrooi\Tokenizer\Matching\Matcher
	 */
	public function parenthesis()
	{
		$matcher = new Matcher;

		$matcher->select($matcher->expr()->anyBetween(
			Lexer::T_PARENTHESIS_OPEN,
			Lexer::T_PARENTHESIS_CLOSE
		));

		$matcher->map(new ResultMapping(function($parenthesis) {
			if (!$parenthesis) {
				return null;
			}

			$value = array_map(function(array $token) {
				return $token['value'];
			}, $parenthesis);
			$value = implode('', $value);

			return new AST\ParenthesisExpression($parenthesis, $value);
		}));

		return $matcher;
	}


	/**
	 * @return \Carrooi\Tokenizer\Matching\Matcher
	 */
	public function newInstance()
	{
		$parenthesisMatcher = new Matcher;

		$parenthesisMatcher->select(
			$parenthesisMatcher->expr()->notRequired(Lexer::T_WHITESPACE),
			$this->parenthesis()
		);

		$matcher = new Matcher;

		$matcher->select(
			Lexer::T_NEW,
			Lexer::T_WHITESPACE,
			$matcher->expr()->anyOf(
				$matcher->expr()->anyOf(Lexer::T_STRING, Lexer::T_NS_SEPARATOR),
				Lexer::T_VARIABLE
			),
			$matcher->expr()->notRequired($parenthesisMatcher)
		);

		$matcher->map(new ResultMapping(function($instance) {
			if (!$instance) {
				return null;
			}

			$nameTokens = array_slice($instance, 2);
			$parenthesis = null;

			// with parenthesis
			if (!Helpers::isValidToken($instance[count($instance) - 1])) {
				$parenthesis = $instance[count($instance) - 1][1];
				$nameTokens = array_slice($nameTokens, 0, -1);
			}

			$name = array_map(function(array $token) {
				return $token['value'];
			}, $nameTokens);
			$name = implode('', $name);

			$class = new AST\NewInstanceExpression(Helpers::flattenTokens($instance), $name);
			$class->parenthesis = $parenthesis;

			return $class;
		}));

		return $matcher;
	}


	/**
	 * @return \Carrooi\Tokenizer\Matching\Matcher
	 */
	public function constant()
	{
		$matcher = new Matcher;

		$matcher->select(
			Lexer::T_CONST,
			Lexer::T_WHITESPACE,
			Lexer::T_STRING
		);

		$matcher->map(new ResultMapping(function($constant) {
			if (!$constant) {
				return null;
			}

			return new AST\ConstantDeclaration($constant, $constant[2]['value']);
		}));

		return $matcher;
	}


	/**
	 * @return \Carrooi\Tokenizer\Matching\Matcher
	 */
	public function classDeclaration()
	{
		$typeMatcher = new Matcher;
		$typeMatcher->select(
			$typeMatcher->expr()->anyOf(
				Lexer::T_FINAL,
				Lexer::T_ABSTRACT
			),
			Lexer::T_WHITESPACE
		);

		$extendsMatcher = new Matcher;
		$extendsMatcher->select(
			Lexer::T_WHITESPACE,
			Lexer::T_EXTENDS,
			Lexer::T_WHITESPACE,
			$extendsMatcher->expr()->anyOf(
				Lexer::T_STRING,
				Lexer::T_NS_SEPARATOR
			)
		);

		$implementsMatcher = new Matcher;
		$implementsMatcher->select(
			Lexer::T_WHITESPACE,
			Lexer::T_IMPLEMENTS,
			Lexer::T_WHITESPACE,
			$implementsMatcher->expr()->anyOf(
				Lexer::T_WHITESPACE,
				Lexer::T_COMMA,
				Lexer::T_STRING,
				Lexer::T_NS_SEPARATOR
			)
		);

		$matcher = new Matcher;

		$matcher->select(
			$matcher->expr()->notRequired($typeMatcher),
			Lexer::T_CLASS,
			Lexer::T_WHITESPACE,
			Lexer::T_STRING,
			$matcher->expr()->notRequired($extendsMatcher),
			$matcher->expr()->notRequired($implementsMatcher)
		);

		$matcher->map(new ResultMapping(function(array $tokens) {
			$class = new ClassDeclaration(Helpers::flattenTokens($tokens), $tokens[3]['value']);

			if ($tokens[0]) {
				if ($tokens[0][0]['type'] === Lexer::T_FINAL) {
					$class->final = true;
				}

				if ($tokens[0][0]['type'] === Lexer::T_ABSTRACT) {
					$class->abstract = true;
				}
			}

			if ($tokens[4]) {
				$extends = array_map(function(array $token) {
					return $token['value'];
				}, array_slice($tokens[4], 3));
				$extends = implode('', $extends);

				$class->extends = $extends;
			}

			if ($tokens[5]) {
				$implements = array_map(function(array $token) {
					return $token['value'];
				}, array_slice($tokens[5], 3));
				$implements = implode('', $implements);
				$implements = explode(',', $implements);
				$implements = array_map(function($implement) {
					return trim($implement);
				}, $implements);

				$class->implements = $implements;
			}

			return $class;
		}));

		return $matcher;
	}

}
