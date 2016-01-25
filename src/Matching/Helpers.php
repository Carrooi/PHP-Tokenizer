<?php

namespace Carrooi\Tokenizer\Matching;

use Carrooi\Tokenizer\Parsing\AST;
use Carrooi\Tokenizer\Parsing\Lexer;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class Helpers
{


	/**
	 * @param int $token
	 * @param int|array $expectedToken
	 * @return bool
	 */
	public static function isTokenA($token, $expectedToken)
	{
		$expectedToken = is_array($expectedToken) ? $expectedToken : [$expectedToken];
		$hasNegative = false;

		foreach ($expectedToken as $expected) {
			$negative = false;

			if ($expected < 0) {
				$hasNegative = true;
				$negative = true;
				$expected = ~$expected;
			}

			if (!$negative && $token === $expected) {
				return true;
			}

			if ($negative && $token === $expected) {
				return false;
			}
		}

		return $hasNegative ? true : false;
	}


	/**
	 * @param mixed $token
	 * @return bool
	 */
	public static function isValidToken($token)
	{
		if ($token instanceof AST\Entity) {
			return true;
		}

		if (!is_array($token)) {
			return false;
		}

		return ['value', 'type', 'position', 'line'] == array_keys($token);
	}


	/**
	 * @param array $tokens
	 * @return bool
	 */
	public static function isListOfValidTokens(array $tokens)
	{
		foreach ($tokens as $token) {
			if (!static::isValidToken($token)) {
				return false;
			}
		}

		return true;
	}


	/**
	 * @param array $tokens
	 * @return array
	 */
	public static function flattenTokens(array $tokens)
	{
		$result = [];

		foreach ($tokens as $token) {
			if (is_array($token) && static::isValidToken($token)) {
				$result[] = $token;

			} elseif (is_array($token)) {
				$result = array_merge($result, static::flattenTokens($token));

			} elseif ($token instanceof AST\Entity) {
				$result = array_merge($result, static::flattenTokens($token->tokens));

			}
		}

		return $result;
	}


	/**
	 * @param array $tokens
	 * @return array
	 */
	public static function getLastToken(array $tokens)
	{
		$last = null;

		for ($i = count($tokens) - 1; $i >= 0; $i--) {
			if ($tokens[$i]) {
				$last = $tokens[$i];
				break;
			}
		}

		if (!$last) {
			return null;
		}

		if ($last instanceof AST\Entity) {
			$last = $last->tokens;
		}

		if (static::isValidToken($last)) {
			return $last;
		}

		return static::getLastToken($last);
	}


	/**
	 * @param \Carrooi\Tokenizer\Parsing\Lexer $lexer
	 * @param array $token
	 */
	public static function moveLexerToToken(Lexer $lexer, array $token)
	{
		if (!$lexer->lookahead || ($lexer->lookahead['position'] > $token['position'])) {
			return;
		}

		while ($lexer->lookahead && $lexer->lookahead !== $token) {
			$lexer->moveNext();
		}

		if ($lexer->lookahead === $token) {
			$lexer->moveNext();
		}
	}

}
