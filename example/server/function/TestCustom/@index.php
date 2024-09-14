<?php
use Krishna\API\Func;
use Krishna\API\Server;

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
	'?_unsigned' => 'unsigned|null',
	'?_url' => 'url|null',
	'?_url64' => 'url64|null',
]);

Func::set_definition(function(array $params, string $function_name) {
	return $params;
});


function array2xml(array $array, \SimpleXMLElement &$xml) {
	// Loop through array
	foreach($array as $key => $value ) {
		// Another array? Iterate
		if (is_array($value)) {
			array2xml($value, $xml->addChild($key));
		} else {
			$xml->addChild($key, $value);
		}
	}
	
	// Return XML
	return $xml->asXML();
}
Server::set_custom_final_writer(function($data) {
	// [
	// 	'status' => $status,
	// 	'value' => $value,
	// 	'headers' => $headers,
	// 	'meta' => $meta
	// ] = $data;

	// Dump the final data
	var_dump($data);
});