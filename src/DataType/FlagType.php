<?php
namespace Krishna\API\DataType;

use Krishna\DataValidator\Returner;

class FlagType implements \Krishna\DataValidator\TypeInterface {
	use \Krishna\Utilities\StaticOnlyTrait;
	const Name = 'flag';
	const Consume = 0;

	public static function validate($value, bool $allow_null = false) : Returner {
		return Returner::valid(true);
	}
}