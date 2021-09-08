<?php
use KrishnaAPI\Func;

Func::set_signature([
	'?msg' => 'String'
]);

Func::set_definition(function(array $param, string $function) {
	$ret = ['Reply from ping'];
	if(array_key_exists('msg', $param)) {
		$ret[] = "Received msg: {$param['msg']}";
	}
	return $ret;
});