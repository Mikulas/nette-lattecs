<?php

namespace Mikulas\LatteCS\Rules;

use Latte\Token;


abstract class Rule
{

	/** @var array of [message, line] */
	private $errors = [];

	/**
	 * @param array $tokens
	 * @return array of [message, line]
	 */
	public function __invoke(array $tokens)
	{
		$this->run($tokens);
		$errors = $this->errors;
		$this->errors = [];
		return $errors;
	}

	/**
	 * @param Token[] $tokens
	 * @return mixed
	 */
	abstract protected function run(array $tokens);

	protected function error($message, $line)
	{
		$this->errors[] = [$message, $line];
	}

}
