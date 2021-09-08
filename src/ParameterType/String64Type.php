<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Base64;
use KrishnaAPI\Returner;

class String64Type implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 1;
	const Name = 'string:{Base64URL encoded}';

	public static function verify($value) : Returner {
		if(is_string($value)) {
			$f = Base64::decode($value, true);
			if($f !== null) {
				return Returner::valid($f);
			}
		}
		return Returner::invalid(static::Name);
	}
}