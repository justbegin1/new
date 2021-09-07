<?php
namespace KrishnaAPI\ParameterType;
use \KrishnaAPI\Returner;

class FloatType implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 1;
	const Name = 'float';

	public static function verify($value) : Returner {
		$f = filter_var($value, FILTER_VALIDATE_FLOAT);
		if($f === false) {
			return Returner::invalid('float');
		}
		return Returner::valid($f);
	}
}