<?php
namespace KrishnaAPI\ParameterType;

use KrishnaAPI\Base64;
use KrishnaAPI\JSON;
use KrishnaAPI\Returner;

class ArrayType implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 1;
	const Name = 'array';

	public static function verify($value) : Returner {
		if(is_array($value)) {
			return Returner::valid($value);
		}
		if(is_string($value)) {
			$f = Base64::decode_json($value);
			if(is_array($f)) {
				return Returner::valid($f);
			}
			$f = JSON::decode($value);
			if(is_array($f)) {
				return Returner::valid($f);
			}
		}
		return Returner::invalid('array');
	}
}