<?php

namespace Carrooi\Tokenizer\Matching;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class ResultMapping
{


	/** @var callable */
	private $fn;


	/**
	 * @param callable $fn
	 */
	public function __construct(callable $fn)
	{
		$this->fn = $fn;
	}


	/**
	 * @param array $result
	 * @return array
	 */
	public function map(array $result)
	{
		return call_user_func($this->fn, $result);
	}

}
