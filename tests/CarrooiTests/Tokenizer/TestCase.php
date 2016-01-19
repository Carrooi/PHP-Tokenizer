<?php

namespace CarrooiTests\Tokenizer;

use Carrooi\Tokenizer\Tokenizer;
use Tester\TestCase as BaseTestCase;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class TestCase extends BaseTestCase
{


	/**
	 * @param string $string
	 * @param int $position
	 * @param int $line
	 * @param bool $literals
	 * @return array
	 */
	protected function createToken($string, $position, $line = 1, $literals = false)
	{
		$openingTag = '<?php';

		if (mb_strtolower($string) === '<?php' || $string === '<?') {
			$openingTag = $string;
			$string = '';
		} else {
			$string = ' '. $string;
		}

		$tokens = Tokenizer::tokenize("$openingTag$string", $literals);

		if (count($tokens) === 1) {
			$token = $tokens[0];

		} elseif (count($tokens) === 2) {
			$token = $tokens[1];
			$token['value'] = mb_substr($token['value'], 1);

		} else {
			$token = $tokens[2];
		}

		$token['position'] = $position;
		$token['line'] = $line;

		return $token;
	}

}
