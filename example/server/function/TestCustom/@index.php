<?php
use Krishna\API\Func;
use Krishna\API\Server;
use Krishna\Utilities\JSON;

Server::set_custom_final_writer(function($data) {
	[
		'status' => $status,
		'value' => $value,
		'headers' => $headers,
		'meta' => $meta
	] = $data;
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Custom Final Writer</title>
	<style>
		body {
			padding: 0.5rem;
			margin: 0;
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		}
		dl {
			padding: 0.8rem;
			box-shadow: 0 0 3px black;
			border-radius: 0.5rem;
			display: grid;
			gap: 0.8rem;
			grid-template-columns: max-content 1fr;
		}
		dt {
			font-weight: bold;
			font-size: 1.1rem;
		}
		dd {
			margin: 0;
		}
		pre {
			font-size: 1.1rem;
			margin: 0;
			white-space: pre-wrap;
			word-break: break-all;
		}
	</style>
</head>
<body>
	<dl>
		<dt>Status:</dt>
		<dd><?= $status->description() ?></dd>
		<dt>Value:</dt>
		<dd><pre><?= JSON::encode($value, true, true) ?></pre></dd>
		<dt>Headers:</dt>
		<dd><pre><?= JSON::encode($headers, true, true) ?></pre></dd>
		<dt>Meta:</dt>
		<dd><pre><?= JSON::encode($meta, true, true) ?></pre></dd>
	</dl>
</body>
</html><?php
});

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