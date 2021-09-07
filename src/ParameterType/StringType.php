<?php
namespace KrishnaAPI\ParameterType;
use \KrishnaAPI\Returner;

class StringType implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 1;
	const Name = 'string';

	public static function verify($value) : Returner {
		if(is_string($value)) {
			return Returner::valid($value);
		}
		return Returner::invalid('string');
	}
}