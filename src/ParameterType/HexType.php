<?php
namespace KrishnaAPI\ParameterType;

use KrishnaAPI\Returner;

class HexType implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 1;
	const Name = 'string:{hex}';

	public static function verify($value) : Returner {
		if(preg_match("/^[0-9a-f]+$/i", $value)) {
			return Returner::valid(hexdec($value));
		}
		return Returner::invalid(static::Name);
	}
}