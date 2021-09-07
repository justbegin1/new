<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Returner;

class FlagType  implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 0;
	const Name = 'flag';

	public static function verify($value) : Returner {
		return Returner::valid(NULL);
	}
}