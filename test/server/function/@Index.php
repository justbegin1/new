<?php

use Krishna\API\Func;

Func::set_definition(function() {
	// Listing available APIs
	$list = array_diff(scandir(__DIR__), ['.', '..', \Krishna\API\Config::$func_default_name . '.php']);
	$ret = [];
	foreach($list as $i) {
		if(str_ends_with($i, '.php')) {
			$ret[] = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', substr($i, 0, -4)));
		}
	}
	return ['available_api' => $ret];
});