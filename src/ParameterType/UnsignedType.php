<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Returner;

class UnsignedType implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 1;
	const Name = 'int:{unsigned}';
	public static function verify($value) : Returner {
		$v = IntType::verify($value);
		if(!$v->valid || $v->value < 0) {
			return Returner::invalid(static::Name);
		}
		return Returner::valid($v->value);
	}
}