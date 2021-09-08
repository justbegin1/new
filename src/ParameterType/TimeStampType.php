<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Returner;

class TimeStampType implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 1;
	const Name = 'string:{Timestamp}';

	public static function verify($value) : Returner {
		if(is_string($value)) {
			$time = strtotime($value);
			if($time !== false) {
				return Returner::valid($time);
			}
		}
		return Returner::invalid(static::Name);
	}
}