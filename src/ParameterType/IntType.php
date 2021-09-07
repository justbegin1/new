<?php
namespace KrishnaAPI\ParameterType;
use \KrishnaAPI\Returner;

class IntType implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 1;
	const Name = 'integer';

	public static function verify($value) : Returner {
		$f = filter_var($value, FILTER_VALIDATE_INT);
		if($f === false) {
			return Returner::invalid('integer');
		}
		return Returner::valid($f);
	}
}