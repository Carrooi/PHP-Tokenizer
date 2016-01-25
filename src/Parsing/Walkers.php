<?php

namespace Carrooi\Tokenizer\Parsing;

use Carrooi\Tokenizer\Matching\Helpers;
use Carrooi\Tokenizer\Matching\Matcher;
use Carrooi\Tokenizer\Matching\ResultMapping;
use Carrooi\Tokenizer\Parsing\AST\ClassDeclaration;
use Carrooi\Tokenizer\Parsing\AST\ClassNameExpression;
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
			$this->className()
		);

		$matcher->map(new ResultMapping(function($namespace) {
			if (!$namespace) {
				return null;
			}

			return new AST\NamespaceDeclaration(Helpers::flattenTokens($namespace), $namespace[2]);
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
	public function className()
	{
		$matcher = new Matcher;

		$matcher->select($matcher->expr()->anyOf(
			Lexer::T_STRING,
			Lexer::T_NS_SEPARATOR
		));

		$matcher->map(new ResultMapping(function($className) {
			if (!$className) {
				return null;
			}

			$name = array_map(function(array $token) {
				return $token['value'];
			}, $className);
			$name = implode('', $name);

			return new ClassNameExpression($className, $name);
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
				$this->className(),
				Lexer::T_VARIABLE
			),
			$matcher->expr()->notRequired($parenthesisMatcher)
		);

		$matcher->map(new ResultMapping(function($instance) {
			if (!$instance) {
				return null;
			}

			$parenthesis = null;

			// with parenthesis
			if (!Helpers::isValidToken($instance[count($instance) - 1])) {
				$parenthesis = $instance[count($instance) - 1][1];
			}

			// ClassNameExpression or $variable
			$name = is_array($instance[2]) ? $instance[2]['value'] : $instance[2];

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
			$this->className()
		);

		$implementsMatcher = new Matcher;
		$implementsMatcher->select(
			Lexer::T_WHITESPACE,
			Lexer::T_IMPLEMENTS,
			Lexer::T_WHITESPACE,
			$implementsMatcher->expr()->anyOf(
				Lexer::T_WHITESPACE,
				Lexer::T_COMMA,
				Lexer::T_STRING,			// todo: use className walker
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

			if ($tokens[4] && $tokens[4][3]) {
				$class->extends = $tokens[4][3];
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
