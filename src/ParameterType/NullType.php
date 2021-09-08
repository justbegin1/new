<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Returner;

class NullType implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 1;
	const Name = 'null';

	public static function verify($value) : Returner {
		if($value === NULL || (is_string($value) && ($value === "" || strcasecmp($value, 'null') === 0))) {
			$f = NULL;
			return Returner::valid($f);
		}
		return Returner::invalid(static::Name);
	}
}