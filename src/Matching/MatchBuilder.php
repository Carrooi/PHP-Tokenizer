<?php

namespace Carrooi\Tokenizer\Matching;

use Carrooi\Tokenizer\InvalidArgumentException;
use Carrooi\Tokenizer\Matching\Modifiers\AbstractModifier;
use Carrooi\Tokenizer\Parsing\Lexer;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class MatchBuilder
{


	/** @var array */
	private $select = [];

	/** @var \Carrooi\Tokenizer\Matching\ResultMapping|null */
	private $resultMapping;


	/**
	 * @param mixed $token
	 * @return $this
	 */
	public function select($token)
	{
		$this->select = [];
		call_user_func_array([$this, 'addSelect'], func_get_args());
		return $this;
	}


	/**
	 * @param mixed $token
	 * @return $this
	 */
	public function addSelect($token)
	{
		$token = is_array($token) ? $token : func_get_args();

		foreach ($token as $t) {
			if (!is_int($t) && !$t instanceof MatchBuilder && !$t instanceof AbstractModifier) {
				throw new InvalidArgumentException('Token can be only integer, modifier or instance of MatchBuilder.');
			}

			$this->select[] = $t;
		}

		return $this;
	}


	/**
	 * @param \Carrooi\Tokenizer\Matching\ResultMapping $mapper
	 */
	public function map(ResultMapping $mapper)
	{
		$this->resultMapping = $mapper;
	}


	/**
	 * @return array
	 */
	public function getSelects()
	{
		return $this->select;
	}


	/**
	 * @param array $tokens
	 * @return array|null
	 */
	public function match(array $tokens)
	{
		$lexer = new Lexer($tokens);
		return $this->doMatch($lexer);
	}


	/**
	 * @param \Carrooi\Tokenizer\Parsing\Lexer $lexer
	 * @return array|null
	 */
	private function doMatch(Lexer $lexer)
	{
		reset($this->select);
		$result = [];

		while (list($key, $select) = each($this->select)) {
			if ($lexer->position === 0) {
				$match = $this->skipUntil($lexer, $select);

			} else {
				$match = $this->_matchToken($lexer, $select);
			}

			// not a first occurrence, reset searching from current position
			if ($match === false) {
				$result = [];
				$select = reset($this->select);
				next($this->select);
				$match = $this->skipUntil($lexer, $select);
			}

			if ($match === false) {
				return null;

			} elseif ($match === true) {
				$result[] = $lexer->lookahead;

			} elseif ($match === null) {
				$result[] = null;

			} elseif (is_array($match)) {
				if (Helpers::isValidToken($match)) {
					$result[] = $match;

				} else {
					$result = array_merge($result, $match);
				}
			}
		}

		return count($result) ? $this->mapResult($result) : null;
	}


	/**
	 * @param array $tokens
	 * @return array|null
	 */
	public function matchAll(array $tokens)
	{
		$result = [];
		$pos = 0;

		while ($pos < count($tokens)) {
			$lexer = new Lexer($tokens);
			$match = $this->doMatch($lexer);

			if ($match) {
				$result[] = $match;
				$pos += $lexer->position;
				$tokens = array_slice($tokens, $pos);
			}
		}

		return count($result) ? $result : null;
	}


	/**
	 * @param \Carrooi\Tokenizer\Parsing\Lexer $lexer
	 * @param mixed $select
	 * @return bool|array|null
	 */
	public function _matchToken(Lexer $lexer, $select)
	{
		$match = false;

		if (is_int($select)) {
			$match = $lexer->isNextToken($select);

		} elseif ($select instanceof MatchBuilder) {
			$tokens = array_slice($lexer->tokens, $lexer->position);
			$match = [$select->match($tokens)];

		} elseif ($select instanceof AbstractModifier) {
			$match = $select->match($lexer);
			$lexer->resetPeek();
		}

		if ($match === true) {
			$match = $lexer->lookahead;
			$lexer->moveNext();

		} elseif ($match) {
			$lastToken = Helpers::getLastToken($match);

			if ($lastToken) {
				Helpers::moveLexerToToken($lexer, $lastToken);
			}
		}

		return $match;
	}


	/**
	 * @return \Carrooi\Tokenizer\Matching\Expressions
	 */
	public function expr()
	{
		return new Expressions($this);
	}


	/**
	 * @param \Carrooi\Tokenizer\Parsing\Lexer $lexer
	 * @param $select
	 * @return bool|array|null
	 */
	private function skipUntil(Lexer $lexer, $select)
	{
		while ($lexer->lookahead) {
			$match = $this->_matchToken($lexer, $select);

			if ($match === false) {
				$lexer->moveNext();

			} else {
				return $match;
			}
		}

		return false;
	}


	/**
	 * @param array $result
	 * @return array
	 */
	private function mapResult(array $result)
	{
		return $this->resultMapping ?
			$this->resultMapping->map($result) :
			$result;
	}

}
