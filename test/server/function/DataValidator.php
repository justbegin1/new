<?php
use Krishna\API\Func;
use Krishna\DataValidator\MultiLinedException;
use Krishna\DataValidator\Validator;

Func::set_signature([
	'struct' => 'json',
	'data' => 'mixed'
]);

Func::set_definition(function($p) {
	try {
		$dv = new Validator($p['struct']);
		$r = $dv->validate($p['data']);
		if($r->valid) {
			return true;
		}
		return $r->error;
	} catch (MultiLinedException $ex) {
		return $ex->getInfo();
	} catch (\Throwable $ex) {
		return 'Invalid data';
	}
	return null;
});