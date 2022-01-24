<?php
namespace Krishna\API\DataType;

use Krishna\API\Base64;
use Krishna\DataValidator\Returner;
use Krishna\DataValidator\Types\StringType;

class String64Type implements \Krishna\DataValidator\TypeInterface {
	use \Krishna\DataValidator\StaticOnlyTrait;
	const Name = 'string:{Base64URL encoded}';

	public static function validate($value, bool $allow_null = false) : Returner {
		if(($value = StringType::validate($value, $allow_null))->valid) {
			if($allow_null && $value->value === null) {
				return $value;
			}
			if(($value = Base64::decode($value->value, true)) !== null) {
				return Returner::valid($value);
			}
		}
		return Returner::invalid(static::Name);
	}
}