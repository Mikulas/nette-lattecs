<?php

namespace Mikulas\LatteCS;

use Latte\Parser;
use Mikulas\LatteCS\Rules\Rule;


class Runner
{

	/** @var \Latte\Parser */
	private $parser;

	/** @var Rule[] */
	private $rules = [];

	/**
	 * @param Parser $parser
	 * @param NULL|Rule[] $rules
	 */
	function __construct(Parser $parser, array $rules = [])
	{
		$this->parser = $parser;

		if ($rules)
		{
			$this->rules = $rules;
		}
		else
		{
			foreach (scandir(__DIR__ . '/Rules') as $file)
			{
				$short = basename($file, '.php');
				if (!in_array($short, ['.', '..', 'Rule']))
				{
					$class = "Mikulas\\LatteCS\\Rules\\$short";
					$this->rules[] = new $class;
				}
			}
		}
	}

	public function addRule(Rule $rule)
	{
		$this->rules[] = $rule;
	}

	public function checkFile($file)
	{
		$content = file_get_contents($file);
		$tokens = $this->parser->parse($content);

		$errors = [];
		foreach ($this->rules as $rule)
		{
			if ($es = $rule($tokens))
			{
				$errors[get_class($rule)] = $es;
			}
		}

		return $errors;
	}

}
