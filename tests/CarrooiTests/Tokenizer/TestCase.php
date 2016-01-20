<?php

namespace CarrooiTests\Tokenizer;

use Tester\TestCase as BaseTestCase;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class TestCase extends BaseTestCase
{


	/**
	 * @param string $string
	 * @param int $type
	 * @param int $position
	 * @param int $line
	 * @param string|null $literal
	 * @return array
	 */
	protected function token($string, $type, $position, $line = 1, $literal = null)
	{
		$token = [
			'value' => $string,
			'type' => $type,
			'position' => $position,
			'line' => $line,
		];

		if ($literal) {
			$token['literal'] = $literal;
		}

		return $token;
	}

}
