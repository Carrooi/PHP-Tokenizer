<?php

namespace Carrooi\Tokenizer\Matching\Modifiers;

use Carrooi\Tokenizer\Matching\Helpers;
use Carrooi\Tokenizer\Matching\Matcher;
use Carrooi\Tokenizer\Parsing\Lexer;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class ListOfModifier extends AbstractModifier
{


	/** @var \Carrooi\Tokenizer\Matching\Matcher */
	private $matcher;

	/** @var array|\Carrooi\Tokenizer\Matching\Matcher */
	private $delimiter;

	/** @var array */
	private $tokens;


	/**
	 * @param \Carrooi\Tokenizer\Matching\Matcher $matcher
	 * @param array|\Carrooi\Tokenizer\Matching\Matcher $delimiter
	 * @param array $tokens
	 */
	public function __construct(Matcher $matcher, array $delimiter, array $tokens)
	{
		$this->matcher = $matcher;
		$this->delimiter = $delimiter;
		$this->tokens = $tokens;
	}


	/**
	 * @param \Carrooi\Tokenizer\Parsing\Lexer $lexer
	 * @return bool|array|null
	 */
	public function match(Lexer $lexer)
	{
		$result = [];
		$pos = $lexer->position;
		$delimiter = null;

		while ($pos < count($lexer->tokens)) {
			if ($delimiter !== null) {
				$pos += count(Helpers::flattenTokens($delimiter));
			}

			$subLexer = $this->createLexer($lexer->tokens, $pos);
			$match = $this->matchTokens($subLexer, $this->tokens);

			if (!$match) {
				break;
			}

			if ($delimiter !== null) {
				$result = $this->mergeMatchToResult($result, $delimiter);
			}

			$result = $this->mergeMatchToResult($result, $match);
			$delimiter = null;

			if ($match) {
				$pos += count(Helpers::flattenTokens($match));
			}

			$subLexer = $this->createLexer($lexer->tokens, $pos);
			$match = $this->matchTokens($subLexer, $this->delimiter);

			if (!$match) {
				break;
			}

			$delimiter = $match;
		}

		return count($result) ? $result : false;
	}


	/**
	 * @param array $tokens
	 * @param int $offset
	 * @return \Carrooi\Tokenizer\Parsing\Lexer
	 */
	private function createLexer(array $tokens, $offset)
	{
		$tokens = array_slice($tokens, $offset);
		return new Lexer($tokens);
	}


	/**
	 * @param \Carrooi\Tokenizer\Parsing\Lexer $lexer
	 * @param array $tokens
	 * @return array|null
	 */
	private function matchTokens(Lexer $lexer, array $tokens)
	{
		$result = [];

		foreach ($tokens as $select) {
			$token = $this->matcher->_matchToken($lexer, $select);

			if ($select instanceof Matcher && $token) {
				$result[] = $token[0];

			} elseif ($token !== false) {
				$result[] = $token;
			}
		}

		return count($result) ? $result : null;
	}


	/**
	 * @param array $result
	 * @param array $match
	 * @return array
	 */
	private function mergeMatchToResult(array $result, $match)
	{
		if (is_array($match) && Helpers::isValidToken($match)) {
			$result[] = $match;

		} elseif (is_array($match)) {
			$result = array_merge($result, $match);
		}

		return $result;
	}

}
