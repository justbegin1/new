<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Returner;

class URLType implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 1;
	const Name = "string:{URL}";
	
	public static function verify($value) : Returner {
		$f = filter_var($value, FILTER_VALIDATE_URL);
		if($f === false) {
			return Returner::invalid(static::Name);
		}
		return Returner::valid($f);
	}
}