<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Returner;

class URL64Type implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 1;
	const Name = "string:{Base64URL encoded URL}";
	
	public static function verify($value) : Returner {
		$f = String64Type::verify($value);
		if($f->valid) {
			$f = filter_var($f->value, FILTER_VALIDATE_URL);
			if($f !== FALSE) {
				return Returner::valid($f);
			}
		}
		return Returner::invalid(static::Name);
	}
}