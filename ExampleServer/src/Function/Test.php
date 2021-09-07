<?php
use KrishnaAPI\Func;

Func::set_signature([
	'id' => 'Int',
	'name' => 'String',
	'?other1' => ['Int', 'Float'],
	'?other2' => ['Int'],
	'?other3' => [
		'Int',
		'String',
		['Int', 'Float']
	]
]);

Func::set_definition(function(array $params, string $function_name) {
	return [
		'func' => $function_name,
		'param' => $params
	];
});