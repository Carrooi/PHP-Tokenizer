<?php

/**
 * Test: Carrooi\Tokenizer\Tokenizer
 *
 * @testCase CarrooiTests\Tokenizer\Tokenizer\Tokenizer_ArrayTest
 * @author David Kudera
 */

namespace CarrooiTests\Tokenizer\Tokenizer;

use Carrooi\Tokenizer\Tokenizer;
use CarrooiTests\Tokenizer\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class Tokenizer_ArrayTest extends TestCase
{


	public function testTokenize_array()
	{
		$code = <<<PHP
<?php

[
	'first' => 'First element',
	'second' => 'Second element',
];

PHP;

		$tokens = Tokenizer::tokenize($code);

		Assert::equal([
			$this->createToken('<?php', 1),
			$this->createToken("\n\n", 6),
			$this->createToken('[', 8, 3),
			$this->createToken("\n", 9, 3),
			$this->createToken("\t", 10, 4),
			$this->createToken("'first'", 11, 4),
			$this->createToken(' ', 18, 4),
			$this->createToken('=>', 19, 4),
			$this->createToken(' ', 21, 4),
			$this->createToken("'First element'", 22, 4),
			$this->createToken(',', 37, 4),
			$this->createToken("\n", 38, 4),
			$this->createToken("\t", 39, 5),
			$this->createToken("'second'", 40, 5),
			$this->createToken(' ', 48, 5),
			$this->createToken('=>', 49, 5),
			$this->createToken(' ', 51, 5),
			$this->createToken("'Second element'", 52, 5),
			$this->createToken(',', 68, 5),
			$this->createToken("\n", 69, 5),
			$this->createToken(']', 70, 6),
			$this->createToken(';', 71, 6),
			$this->createToken("\n", 72, 6),
		], $tokens);
	}

}

run(new Tokenizer_ArrayTest());
