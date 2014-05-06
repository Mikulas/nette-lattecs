<?php

namespace Mikulas\LatteCS\Rules;

use Latte\Token;


abstract class Rule
{

	/** @var array of [message, line] */
	private $errors = [];

	/**
	 * @param array $tokens
	 * @param string $content
	 * @return array of [message, line]
	 */
	public function __invoke(array $tokens, $content)
	{
		$this->run($tokens, $content);
		$errors = $this->errors;
		$this->errors = [];
		return $errors;
	}

	/**
	 * @param Token[] $tokens
	 * @param string $content
	 * @return mixed
	 */
	abstract protected function run(array $tokens, $content);

	protected function error($code, $message, $line)
	{
		$code = str_replace('\\', '.', get_class($this)) . ".$code";
		$this->errors[] = [$code, $message, $line];
	}

}
