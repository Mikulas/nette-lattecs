<?php

use Tester\Assert;
require __DIR__ . '/bootstrap.php';

$rules = [new Mikulas\LatteCS\Rules\BlockIndentation];
$runner = new \Mikulas\LatteCS\Runner(new \Latte\Parser, $rules);

Assert::same([
	[
		'code' => 'Mikulas.LatteCS.Rules.BlockIndentation.Indentation',
		'message' => 'Content in block #content must be indented',
		'line' => 2,
		'rule' => 'Mikulas\LatteCS\Rules\BlockIndentation'
	],
], $runner->checkFile(__DIR__ . '/fixtures/indent.latte'));
