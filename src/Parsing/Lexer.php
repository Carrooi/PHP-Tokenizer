<?php

namespace Carrooi\Tokenizer\Parsing;

use Carrooi\Tokenizer\Matching\Helpers;
use Carrooi\Tokenizer\Parsing\AST;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class Lexer
{


	const T_UNKNOWN = -1;
	const T_NEW_LINE = 10000;
	const T_TAB = 10001;
	const T_SEMICOLON = 10002;
	const T_BRACES_OPEN = 10003;
	const T_BRACES_CLOSE = 10004;
	const T_PARENTHESIS_OPEN = 10005;
	const T_PARENTHESIS_CLOSE = 10006;
	const T_COMMA = 10007;
	const T_EQUAL = 10008;
	const T_CONCAT = 10009;
	const T_COLON = 10010;
	const T_MINUS = 10011;
	const T_PLUS = 10012;
	const T_IS_GREATER = 10013;
	const T_IS_SMALLER = 10014;
	const T_MULTIPLY = 10015;
	const T_DIVIDE = 10016;
	const T_QUESTION_MARK = 10017;
	const T_MODULO = 10018;
	const T_EXCLAMATION_MARK = 10019;
	const T_AMPERSAND = 10020;
	const T_SQUARE_BRACKET_OPEN = 10021;
	const T_SQUARE_BRACKET_CLOSE = 10022;
	const T_AROBAS = 10023;
	const T_QUOTE = 10024;
	const T_DOLLAR = 10025;
	const T_TRUE = 10026;
	const T_FALSE = 10027;
	const T_NULL = 10028;

	const T_ABSTRACT = T_ABSTRACT;
	const T_AND_EQUAL = T_AND_EQUAL;
	const T_ARRAY = T_ARRAY;
	const T_ARRAY_CAST = T_ARRAY_CAST;
	const T_AS = T_AS;
	const T_BAD_CHARACTER = T_BAD_CHARACTER;
	const T_BOOLEAN_AND = T_BOOLEAN_AND;
	const T_BOOLEAN_OR = T_BOOLEAN_OR;
	const T_BOOL_CAST = T_BOOL_CAST;
	const T_BREAK = T_BREAK;
	const T_CALLABLE = T_CALLABLE;
	const T_CASE = T_CASE;
	const T_CATCH = T_CATCH;
	const T_CHARACTER = T_CHARACTER;
	const T_CLASS = T_CLASS;
	const T_CLASS_C = T_CLASS_C;
	const T_CLONE = T_CLONE;
	const T_CLOSE_TAG = T_CLOSE_TAG;
	const T_COMMENT = T_COMMENT;
	const T_CONCAT_EQUAL = T_CONCAT_EQUAL;
	const T_CONST = T_CONST;
	const T_CONSTANT_ENCAPSED_STRING = T_CONSTANT_ENCAPSED_STRING;
	const T_CONTINUE = T_CONTINUE;
	const T_CURLY_OPEN = T_CURLY_OPEN;
	const T_DEC = T_DEC;
	const T_DECLARE = T_DECLARE;
	const T_DEFAULT = T_DEFAULT;
	const T_DIR = T_DIR;
	const T_DIV_EQUAL = T_DIV_EQUAL;
	const T_DNUMBER = T_DNUMBER;
	const T_DOC_COMMENT = T_DOC_COMMENT;
	const T_DO = T_DO;
	const T_DOLLAR_OPEN_CURLY_BRACES = T_DOLLAR_OPEN_CURLY_BRACES;
	const T_DOUBLE_ARROW = T_DOUBLE_ARROW;
	const T_DOUBLE_CAST = T_DOUBLE_CAST;
	const T_DOUBLE_COLON = T_DOUBLE_COLON;
	const T_ECHO = T_ECHO;
	const T_ELLIPSIS = T_ELLIPSIS;
	const T_ELSE = T_ELSE;
	const T_ELSEIF = T_ELSEIF;
	const T_EMPTY = T_EMPTY;
	const T_ENCAPSED_AND_WHITESPACE = T_ENCAPSED_AND_WHITESPACE;
	const T_ENDDECLARE = T_ENDDECLARE;
	const T_ENDFOR = T_ENDFOR;
	const T_ENDFOREACH = T_ENDFOREACH;
	const T_ENDIF = T_ENDIF;
	const T_ENDSWITCH = T_ENDSWITCH;
	const T_ENDWHILE = T_ENDWHILE;
	const T_END_HEREDOC = T_END_HEREDOC;
	const T_EVAL = T_EVAL;
	const T_EXIT = T_EXIT;
	const T_EXTENDS = T_EXTENDS;
	const T_FILE = T_FILE;
	const T_FINAL = T_FINAL;
	const T_FINALLY = T_FINALLY;
	const T_FOR = T_FOR;
	const T_FOREACH = T_FOREACH;
	const T_FUNCTION = T_FUNCTION;
	const T_FUNC_C = T_FUNC_C;
	const T_GLOBAL = T_GLOBAL;
	const T_GOTO = T_GOTO;
	const T_HALT_COMPILER = T_HALT_COMPILER;
	const T_IF = T_IF;
	const T_IMPLEMENTS = T_IMPLEMENTS;
	const T_INC = T_INC;
	const T_INCLUDE = T_INCLUDE;
	const T_INCLUDE_ONCE = T_INCLUDE_ONCE;
	const T_INLINE_HTML = T_INLINE_HTML;
	const T_INSTANCEOF = T_INSTANCEOF;
	const T_INSTEADOF = T_INSTEADOF;
	const T_INT_CAST = T_INT_CAST;
	const T_INTERFACE = T_INTERFACE;
	const T_ISSET = T_ISSET;
	const T_IS_EQUAL = T_IS_EQUAL;
	const T_IS_GREATER_OR_EQUAL = T_IS_GREATER_OR_EQUAL;
	const T_IS_IDENTICAL = T_IS_IDENTICAL;
	const T_IS_NOT_EQUAL = T_IS_NOT_EQUAL;
	const T_IS_NOT_IDENTICAL = T_IS_NOT_IDENTICAL;
	const T_IS_SMALLER_OR_EQUAL = T_IS_SMALLER_OR_EQUAL;
	const T_SPACESHIP = T_SPACESHIP;
	const T_LINE = T_LINE;
	const T_LIST = T_LIST;
	const T_LNUMBER = T_LNUMBER;
	const T_LOGICAL_AND = T_LOGICAL_AND;
	const T_LOGICAL_OR = T_LOGICAL_OR;
	const T_LOGICAL_XOR = T_LOGICAL_XOR;
	const T_METHOD_C = T_METHOD_C;
	const T_MINUS_EQUAL = T_MINUS_EQUAL;
	const T_MOD_EQUAL = T_MOD_EQUAL;
	const T_MUL_EQUAL = T_MUL_EQUAL;
	const T_NAMESPACE = T_NAMESPACE;
	const T_NS_C = T_NS_C;
	const T_NS_SEPARATOR = T_NS_SEPARATOR;
	const T_NEW = T_NEW;
	const T_NUM_STRING = T_NUM_STRING;
	const T_OBJECT_CAST = T_OBJECT_CAST;
	const T_OBJECT_OPERATOR = T_OBJECT_OPERATOR;
	const T_OPEN_TAG = T_OPEN_TAG;
	const T_OPEN_TAG_WITH_ECHO = T_OPEN_TAG_WITH_ECHO;
	const T_OR_EQUAL = T_OR_EQUAL;
	const T_PAAMAYIM_NEKUDOTAYIM = T_PAAMAYIM_NEKUDOTAYIM;
	const T_PLUS_EQUAL = T_PLUS_EQUAL;
	const T_POW = T_POW;
	const T_POW_EQUAL = T_POW_EQUAL;
	const T_PRINT = T_PRINT;
	const T_PRIVATE = T_PRIVATE;
	const T_PUBLIC = T_PUBLIC;
	const T_PROTECTED = T_PROTECTED;
	const T_REQUIRE = T_REQUIRE;
	const T_REQUIRE_ONCE = T_REQUIRE_ONCE;
	const T_RETURN = T_RETURN;
	const T_SL = T_SL;
	const T_SL_EQUAL = T_SL_EQUAL;
	const T_SR = T_SR;
	const T_SR_EQUAL = T_SR_EQUAL;
	const T_START_HEREDOC = T_START_HEREDOC;
	const T_STATIC = T_STATIC;
	const T_STRING = T_STRING;
	const T_STRING_CAST = T_STRING_CAST;
	const T_STRING_VARNAME = T_STRING_VARNAME;
	const T_SWITCH = T_SWITCH;
	const T_THROW = T_THROW;
	const T_TRAIT = T_TRAIT;
	const T_TRAIT_C = T_TRAIT_C;
	const T_TRY = T_TRY;
	const T_UNSET = T_UNSET;
	const T_UNSET_CAST = T_UNSET_CAST;
	const T_USE = T_USE;
	const T_VAR = T_VAR;
	const T_VARIABLE = T_VARIABLE;
	const T_WHILE = T_WHILE;
	const T_WHITESPACE = T_WHITESPACE;
	const T_XOR_EQUAL = T_XOR_EQUAL;
	const T_YIELD = T_YIELD;


	/** @var \Carrooi\Tokenizer\Parsing\Walkers */
	private $walkers;


	/** @var array */
	public $tokens;

	/** @var int */
	public $peekPosition = 0;

	/** @var int */
	public $position = 0;

	/** @var array */
	public $lookahead;

	/** @var array */
	public $token;


	/**
	 * @param array $tokens
	 */
	public function __construct(array $tokens)
	{
		$this->walkers = new Walkers($this);
		$this->tokens = $tokens;

		$this->token = isset($this->tokens[0]) ? $this->tokens[0] : null;
		$this->lookahead = isset($this->tokens[1]) ? $this->tokens[1] : null;
	}


	public function reset()
	{
		$this->token = isset($this->tokens[0]) ? $this->tokens[0] : null;
		$this->lookahead = isset($this->tokens[1]) ? $this->tokens[1] : null;
		$this->peekPosition = 0;
		$this->position = 0;
	}


	public function resetPeek()
	{
		$this->peekPosition = 0;
	}


	/**
	 * @param int|array $token
	 * @return bool
	 */
	public function isCurrentToken($token)
	{
		return Helpers::isTokenA($this->token['type'], $token);
	}


	/**
	 * @param int|array $token
	 * @return bool
	 */
	public function isNextToken($token)
	{
		return $this->lookahead && Helpers::isTokenA($this->lookahead['type'], $token);
	}


	/**
	 * @return bool
	 */
	public function moveNext()
	{
		$this->peekPosition = 0;

		if ($this->lookahead) {
			$this->position++;

			$this->token = $this->lookahead;
			$this->lookahead = isset($this->tokens[$this->position + 1]) ? $this->tokens[$this->position + 1] : null;
		}

		return $this->lookahead !== null;
	}


	/**
	 * @return bool
	 */
	public function moveBack()
	{
		$this->peekPosition = 0;

		if (isset($this->tokens[$this->position - 1])) {
			$this->position--;

			$this->lookahead = $this->token;
			$this->token = $this->tokens[$this->position];
		}

		return isset($this->tokens[$this->position - 1]);
	}


	/**
	 * @param int|array $type
	 * @param int|array|null $stopAt
	 * @return bool
	 */
	public function skipUntil($type, $stopAt = null)
	{
		$type = is_array($type) ? $type : [$type];
		$stopAt = is_array($stopAt) ? $stopAt : ($stopAt ? [$stopAt] : []);

		while (
			$this->lookahead !== null &&
			!Helpers::isTokenA($this->lookahead['type'], $type) &&
			(empty($stopAt) || !Helpers::isTokenA($this->token['type'], $stopAt))
		) {
			$this->moveNext();
		}

		return $this->lookahead && Helpers::isTokenA($this->lookahead['type'], $type);
	}


	public function currentPeek()
	{
		return isset($this->tokens[$this->position + $this->peekPosition]) ? $this->tokens[$this->position + $this->peekPosition] : null;
	}


	/**
	 * @return array|null
	 */
	public function peek()
	{
		if (isset($this->tokens[$this->position + $this->peekPosition + 1])) {
			return $this->tokens[$this->position + ++$this->peekPosition];
		} else {
			return null;
		}
	}


	/**
	 * @return array|null
	 */
	public function glimpse()
	{
		$peekPosition = $this->peekPosition;
		$peek = $this->peek();
		$this->peekPosition = $peekPosition;
		return $peek;
	}


	/**
	 * @return int
	 */
	public function count()
	{
		return count($this->tokens);
	}


	/**
	 * @return \Carrooi\Tokenizer\Parsing\AST\NumberExpression|null
	 */
	public function walkNumber()
	{
		return $this->walkers->number()->match($this);
	}


	/**
	 * @return \Carrooi\Tokenizer\Parsing\AST\NamespaceDeclaration|null
	 */
	public function walkNamespaceDeclaration()
	{
		return $this->walkers->namespaceDeclaration()->match($this);
	}


	/**
	 * @return \Carrooi\Tokenizer\Parsing\AST\ParenthesisExpression|null
	 */
	public function walkParenthesis()
	{
		return $this->walkers->parenthesis()->match($this);
	}


	/**
	 * @return \Carrooi\Tokenizer\Parsing\AST\NewInstanceExpression|null
	 */
	public function walkNewInstance()
	{
		return $this->walkers->newInstance()->match($this);
	}


	/**
	 * @return \Carrooi\Tokenizer\Parsing\AST\ConstantDeclaration|null
	 */
	public function walkConstant()
	{
		return $this->walkers->constant()->match($this);
	}


	/**
	 * @return \Carrooi\Tokenizer\Parsing\AST\ClassDeclaration|null
	 */
	public function walkClassDeclaration()
	{
		return $this->walkers->classDeclaration()->match($this);
	}


	/**
	 * @param integer $token
	 * @return string
	 */
	public static function getLiteral($token)
	{
		$reflClass = new \ReflectionClass(__CLASS__);
		$constants = $reflClass->getConstants();

		$negative = false;
		if ($token < 0) {
			$negative = true;
			$token = ~$token;
		}

		foreach ($constants as $name => $value) {
			if ($value === $token) {
				return ($negative ? '~' : ''). $name;
			}
		}

		return null;
	}

}
