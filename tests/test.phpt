<?php

use Tester\Assert;
require __DIR__ . '/bootstrap.php';

$parser = new \Latte\Parser();
$runner = new \Mikulas\LatteCS\Runner($parser);
$runner->addRule(new \Mikulas\LatteCS\Rules\ParamsCommentInEachBlock);

Assert::same([
	'Mikulas\LatteCS\Rules\ParamsCommentInEachBlock' => [
		['Block #content is not properly annotated', 3]
	]
], $runner->checkFile(__DIR__ . '/fixtures/missing.latte'));

Assert::same([
	'Mikulas\LatteCS\Rules\ParamsCommentInEachBlock' => [
		['Lattedoc does not start with two stars but it should', 5],
	]
], $runner->checkFile(__DIR__ . '/fixtures/missing_star.latte'));

Assert::same([
	'Mikulas\LatteCS\Rules\ParamsCommentInEachBlock' => [
		['Block #content is annotated, but no @param was specified.', 5],
	]
], $runner->checkFile(__DIR__ . '/fixtures/missing_params.latte'));

Assert::same([
	'Mikulas\LatteCS\Rules\ParamsCommentInEachBlock' => [
		["Invalid annotation '@param xoxo', expected '@param type \$name'", 5],
	]
], $runner->checkFile(__DIR__ . '/fixtures/invalid_param.latte'));

Assert::same([
	'Mikulas\LatteCS\Rules\ParamsCommentInEachBlock' => [
		["Invalid annotation '@param string basePath', variable name must start with '$'", 5],
	]
], $runner->checkFile(__DIR__ . '/fixtures/invalid_param_dollar.latte'));

Assert::same([], $runner->checkFile(__DIR__ . '/fixtures/valid.latte'));
