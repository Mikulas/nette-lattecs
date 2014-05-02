<?php

use Tester\Assert;
require __DIR__ . '/bootstrap.php';

$rules = [new Mikulas\LatteCS\Rules\BlockAnnotation];
$runner = new \Mikulas\LatteCS\Runner(new \Latte\Parser, $rules);

assertError($runner, 'missing.latte', 'Block #content is not properly annotated', 3);
assertError($runner, 'missing_star.latte', 'Lattedoc does not start with two stars but it should', 5);
assertError($runner, 'missing_params.latte', 'Block #content is annotated, but no @param was specified.', 5);
assertError($runner, 'invalid_param.latte', "Invalid annotation '@param xoxo', expected '@param type \$name'", 5);
assertError($runner, 'invalid_param_dollar.latte', "Invalid annotation '@param string basePath', variable name must start with '$'", 5);

Assert::same([], $runner->checkFile(__DIR__ . '/fixtures/inline.latte'));

$rule = 'Mikulas\LatteCS\Rules\BlockAnnotation';
Assert::same([
	['message' => 'Block #content is not properly annotated', 'line' => 2, 'rule' => $rule],
	['message' => 'Block #foo is not properly annotated', 'line' => 4, 'rule' => $rule],
	['message' => 'Block #bar is not properly annotated', 'line' => 6, 'rule' => $rule],
], $runner->checkFile(__DIR__ . '/fixtures/multiple.latte'));

Assert::same([], $runner->checkFile(__DIR__ . '/fixtures/valid.latte'));

function assertError($runner, $file, $message, $line)
{
	$real = $runner->checkFile(__DIR__ . "/fixtures/$file");
	Assert::same([
		[
			'message' => $message,
			'line' => $line,
			'rule' => 'Mikulas\LatteCS\Rules\BlockAnnotation',
		]
	], $real);
}
