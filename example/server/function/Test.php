<?php
use Krishna\API\Func;

Func::set_signature([
	'?a' => 'int',
	'?b' => 'int|float',
	'?c' => 'url64|null'
]);

Func::set_definition(function(array $params, string $function_name) {
	return [
		'func' => $function_name,
		'param' => $params
	];
});