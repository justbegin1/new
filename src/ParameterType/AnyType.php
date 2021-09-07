<?php
namespace KrishnaAPI\ParameterType;

use KrishnaAPI\Returner;

class AnyType implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 1;
	const Name = "any";
	
	public static function verify($value) : Returner {
		return Returner::valid($value);
	}
}