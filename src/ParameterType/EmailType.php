<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Returner;


class EmailType implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 1;
	const Name = 'string:{email}';

	public static function verify($value) : Returner {
		$f = filter_var($value, FILTER_VALIDATE_EMAIL);
		if($f === false) {
			return Returner::invalid('string:{email}');
		}
		return Returner::valid($f);
	}
}