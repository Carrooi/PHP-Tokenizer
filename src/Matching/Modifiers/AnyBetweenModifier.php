<?php

namespace Carrooi\Tokenizer\Matching\Modifiers;

use Carrooi\Tokenizer\Matching\Helpers;
use Carrooi\Tokenizer\Matching\Matcher;
use Carrooi\Tokenizer\Parsing\Lexer;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class AnyBetweenModifier extends AbstractModifier
{


	/** @var \Carrooi\Tokenizer\Matching\Matcher */
	private $builder;

	/** @var int|\Carrooi\Tokenizer\Matching\Modifiers\AbstractModifier */
	private $startToken;

	/** @var int|\Carrooi\Tokenizer\Matching\Modifiers\AbstractModifier */
	private $endToken;

	/** @var bool */
	private $recursive;


	/**
	 * @param \Carrooi\Tokenizer\Matching\Matcher $builder
	 * @param int|\Carrooi\Tokenizer\Matching\Modifiers\AbstractModifier $startToken
	 * @param int|\Carrooi\Tokenizer\Matching\Modifiers\AbstractModifier $endToken
	 * @param bool $recursive
	 */
	public function __construct(Matcher $builder, $startToken, $endToken, $recursive = false)
	{
		$this->builder = $builder;
		$this->startToken = $startToken;
		$this->endToken = $endToken;
		$this->recursive = $recursive;
	}


	/**
	 * @param \Carrooi\Tokenizer\Parsing\Lexer $lexer
	 * @return array|bool
	 */
	function match(Lexer $lexer)
	{
		$startToken = $this->startToken instanceof AbstractModifier ?
			$this->builder->_matchToken($lexer, $this->startToken) :
			$this->startToken;

		$endToken = $this->endToken instanceof AbstractModifier ?
			$this->builder->_matchToken($lexer, $this->endToken) :
			$this->endToken;

		if (!$lexer->isNextToken($startToken)) {
			return false;
		}

		$openings = 0;
		$result = [];

		while (true) {
			$peek = $lexer->peek();

			if (!$peek) {
				break;
			}

			if (Helpers::isTokenA($peek['type'], $startToken)) {
				$openings++;

				if ($openings > 1 && $this->recursive) {
					$subTokens = array_slice($lexer->tokens, $lexer->position + $lexer->peekPosition - 1);
					$subLexer = new Lexer($subTokens);

					$parenthesis = $this->match($subLexer);
					if (!$parenthesis) {
						return false;
					}

					$moveTo = $subLexer->peekPosition - 1;
					for ($i = 0; $i < $moveTo; $i++) {
						$lexer->peek();
					}

					$openings--;
					$result[] = $parenthesis;

				} else {
					$result[] = $peek;
				}

			} else {
				$result[] = $peek;

				if (Helpers::isTokenA($peek['type'], $endToken) && --$openings < 1) {
					break;
				}

			}
		}

		return count($result) ? $result : false;
	}

}
