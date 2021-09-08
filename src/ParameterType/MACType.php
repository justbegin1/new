<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Returner;

class MACType implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 1;
	const Name = 'string:{MAC address}';

	public static function verify($value) : Returner {
		$f = filter_var($value, FILTER_VALIDATE_MAC);
		if($f === false) {
			return Returner::invalid(static::Name);
		}
		return Returner::valid($f);
	}
}