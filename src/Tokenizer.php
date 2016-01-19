<?php

namespace Carrooi\Tokenizer;

use Carrooi\Tokenizer\Parsing\Lexer;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class Tokenizer
{


	/** @var array */
	private static $squashableSpaces = [
		' ' => Lexer::T_WHITESPACE,
		'\r\n|\n|\r' => Lexer::T_NEW_LINE,
		'\t' => Lexer::T_TAB,
	];


	/**
	 * @param string $input
	 * @param bool $literals
	 * @return array
	 */
	public static function tokenize($input, $literals = false)
	{
		$original = token_get_all($input);
		$tokens = [];

		$original = self::prepareTokens($original);

		$pos = 1;
		$previous = null;
		for ($i = 0; $i < count($original); $i++) {
			$token = $original[$i];
			$next = isset($original[$i + 1]) ? $original[$i + 1] : null;
			$append = null;

			if (!is_array($token)) {
				$token = self::tokenizeChar($token, $previous);
			}

			// fix ending spaces
			foreach (self::$squashableSpaces as $space => $spaceType) {

				// is value ending with space and begin with something else?
				if (preg_match('/^([^'. $space. ']+?)('. $space. ')$/', $token[1], $match)) {

					// move ending spaces to next spaces token
					if (is_array($next) && preg_match('/^['. $space. ']+$/', $next[1])) {
						$token[1] = $match[1];
						$original[$i + 1][1] = $match[2]. $next[1];
						$original[$i + 1][2] = $token[2];

					// append new space token
					} else {
						$token[1] = $match[1];
						$append = [$spaceType, $match[2], $token[2]];
					}

					break;
				}

				// transform T_WHITESPACE token names
				if (preg_match('/^('. $space. ')+$/', $token[1], $match)) {
					$token[0] = $spaceType;
				}
			}

			$current = [
				'value' => $token[1],
				'type' => $token[0],
				'position' => $pos,
				'line' => $token[2],
			];

			if ($literals) {
				$current['literal'] = Lexer::getLiteral($current['type']);
			}

			$tokens[] = $previous = $current;

			$pos += mb_strlen($token[1], 'UTF-8');

			if ($append) {
				$current = [
					'value' => $append[1],
					'type' => $append[0],
					'position' => $pos,
					'line' => $append[2],
				];

				if ($literals) {
					$current['literal'] = Lexer::getLiteral($current['type']);
				}

				$tokens[] = $previous = $current;

				$pos += mb_strlen($append[1], 'UTF-8');
			}
		}

		foreach ($tokens as $i => &$token) {
			if ($token['type'] === Lexer::T_STRING && ($replace = self::postTransformNativeStringTokens($tokens, $token, $i))) {
				$token['type'] = $replace;

				if ($literals) {
					$token['literal'] = Lexer::getLiteral($token['type']);
				}
			}
		}

		return $tokens;
	}


	/**
	 * @param array $tokens
	 * @param array $token
	 * @param int $position
	 * @return int|null
	 */
	private static function postTransformNativeStringTokens(array $tokens, array $token, $position)
	{
		// transform booleans to T_TRUE or T_FALSE tokens
		if (mb_strtolower($token['value'], 'UTF-8') === 'true') {
			return Lexer::T_TRUE;

		} elseif (mb_strtolower($token['value'], 'UTF-8') === 'false') {
			return Lexer::T_FALSE;
		}

		// transform nulls to T_NULL tokens
		if (mb_strtolower($token['value'], 'UTF-8') === 'null') {
			return Lexer::T_NULL;
		}

		return null;
	}


	/**
	 * @param array $tokens
	 * @return array
	 */
	private static function prepareTokens(array $tokens)
	{
		$result = [];

		for ($i = 0; $i < count($tokens); $i++) {
			$token = $tokens[$i];

			if (isset($token['skip'])) {
				continue;
			}

			$spaces = implode('|', array_keys(self::$squashableSpaces));

			if ($token[0] === Lexer::T_INLINE_HTML) {
				if (preg_match('/^['. $spaces. ']+$/', $token[1])) {
					$token[0] = Lexer::T_WHITESPACE;

				} elseif (mb_strtolower($token[1], 'UTF-8') === '<?php' || $token[1] === '<?') {
					$token[0] = Lexer::T_OPEN_TAG;
				}
			}

			// <?php can be two tokens, so let's squash them together
			if (
				($token[0] === Lexer::T_OPEN_TAG) &&
				(isset($tokens[$i + 1]) && $tokens[$i + 1][0] === Lexer::T_STRING && mb_strtolower($tokens[$i + 1][1], 'UTF-8') === 'php')
			) {
				$token[1] .= $tokens[$i + 1][1];
				$tokens[$i + 1]['skip'] = true;
			}

			if ($token[0] !== Lexer::T_WHITESPACE) {
				$result[] = $token;
				continue;
			}

			$spaces = implode('|', array_map(function($space) { return '['. $space. ']+'; }, array_keys(self::$squashableSpaces)));

			preg_match_all('/('. $spaces. ')/', $token[1], $match);
			$match = array_filter($match[0], function($m) { return $m !== ''; });

			$line = $token[2];

			foreach ($match as $m) {
				foreach (self::$squashableSpaces as $space => $spaceType) {
					if (preg_match('/^['. $space. ']+$/', $m)) {
						$result[] = [
							$spaceType,
							$m,
							$line,
						];

						if ($spaceType === Lexer::T_NEW_LINE) {
							$linesCount = preg_match_all('/(\r\n|\n|\r)/', $m);
							$line += $linesCount;
						}
					}
				}
			}
		}

		return $result;
	}


	/**
	 * @see https://github.com/PHPCheckstyle/phpcheckstyle
	 * @param string $s
	 * @param array $previous
	 * @return array
	 */
	private static function tokenizeChar($s, array $previous = null)
	{
		switch ($s) {
			case ';':
				$type = Lexer::T_SEMICOLON;
				break;
			case '{':
				$type = Lexer::T_BRACES_OPEN;
				break;
			case '}':
				$type = Lexer::T_BRACES_CLOSE;
				break;
			case '(':
				$type = Lexer::T_PARENTHESIS_OPEN;
				break;
			case ')':
				$type = Lexer::T_PARENTHESIS_CLOSE;
				break;
			case ',':
				$type = Lexer::T_COMMA;
				break;
			case '=':
				$type = Lexer::T_EQUAL;
				break;
			case '.':
				$type = Lexer::T_CONCAT;
				break;
			case ':':
				$type = Lexer::T_COLON;
				break;
			case '-':
				$type = Lexer::T_MINUS;
				break;
			case '+':
				$type = Lexer::T_PLUS;
				break;
			case '>':
				$type = Lexer::T_IS_GREATER;
				break;
			case '<':
				$type = Lexer::T_IS_SMALLER;
				break;
			case '*':
				$type = Lexer::T_MULTIPLY;
				break;
			case '/':
				$type = Lexer::T_DIVIDE;
				break;
			case '?':
				$type = Lexer::T_QUESTION_MARK;
				break;
			case '%':
				$type = Lexer::T_MODULO;
				break;
			case '!':
				$type = Lexer::T_EXCLAMATION_MARK;
				break;
			case '&':
				$type = Lexer::T_AMPERSAND;
				break;
			case '[':
				$type = Lexer::T_SQUARE_BRACKET_OPEN;
				break;
			case ']':
				$type = Lexer::T_SQUARE_BRACKET_CLOSE;
				break;
			case '@':
				$type = Lexer::T_AROBAS;
				break;
			case '"':
				$type = Lexer::T_QUOTE;
				break;
			case '$':
				$type = Lexer::T_DOLLAR;
				break;
			default:
				$type = Lexer::T_UNKNOWN;
				break;
		}

		$line = null;

		if ($previous && $previous['type'] === Lexer::T_NEW_LINE) {
			$linesCount = preg_match_all('/(\r\n|\n|\r)/', $previous['value']);
			$line = $previous['line'] + $linesCount;

		} elseif ($previous) {
			$line = $previous['line'];
		}

		$token = [
			$type,
			$s,
			$line,
		];

		return $token;
	}

}
