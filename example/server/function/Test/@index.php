<?php
use Krishna\API\Func;

Func::set_signature([
	'?bool' => 'bool',
	'?email' => 'email',
	'?flag' => 'flag',
	'?float' => 'float',
	'?hex' => 'hex',
	'?int' => 'int',
	'?ipv4' => 'ipv4',
	'?ipv6' => 'ipv6',
	'?json' => 'json',
	'?json64' => 'json64',
	'?mac' => 'mac',
	'?mixed' => 'mixed',
	'?number' => 'number',
	'?string' => 'string',
	'?string64' => 'string64',
	'?timestamp' => 'timestamp',
	'?timestamp_utc' => 'timestamp_utc',
	'?unsigned' => 'unsigned',
	'?url' => 'url',
	'?url64' => 'url64',
	// Nullables
	'?_bool' => 'bool|null',
	'?_email' => 'email|null',
	'?_flag' => 'flag|null',
	'?_float' => 'float|null',
	'?_hex' => 'hex|null',
	'?_int' => 'int|null',
	'?_ipv4' => 'ipv4|null',
	'?_ipv6' => 'ipv6|null',
	'?_json' => 'json|null',
	'?_json64' => 'json64|null',
	'?_mac' => 'mac|null',
	'?_mixed' => 'mixed|null',
	'?_number' => 'number|null',
	'?_string' => 'string|null',
	'?_string64' => 'string64|null',
	'?_timestamp' => 'timestamp|null',
	'?_timestamp_utc' => 'timestamp_utc|null',
	'?_unsigned' => 'unsigned|null',
	'?_url' => 'url|null',
	'?_url64' => 'url64|null',
]);

Func::set_definition(function(array $params, string $function_name) {
	return $params;
});