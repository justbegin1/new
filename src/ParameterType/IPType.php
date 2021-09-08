<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Returner;

class IPType implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 1;
	const Name = 'string:{IP address}';

	public static function verify($value) : Returner {
		$f = filter_var($value, FILTER_VALIDATE_IP);
		if($f === false) {
			return Returner::invalid(static::Name);
		}
		return Returner::valid($f);
	}
}