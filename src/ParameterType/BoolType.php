<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Returner;

class BoolType implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 1;
	const Name = 'bool';

	public static function verify($value) : Returner {
		$f = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if($f === null) {
			return Returner::invalid(static::Name);
		}
		return Returner::valid($f);
	}
}