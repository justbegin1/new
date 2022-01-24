<?php
namespace Krishna\API\DataType;

use Krishna\DataValidator\Returner;
use Krishna\DataValidator\Types\URLType;

class URL64Type implements \Krishna\DataValidator\TypeInterface {
	use \Krishna\DataValidator\StaticOnlyTrait;
	const Name = 'string:{Base64URL encoded URL}';

	public static function validate($value, bool $allow_null = false): Returner {
		if(($value = String64Type::validate($value, $allow_null))->valid) {
			if($allow_null && $value->value === null) {
				return $value;
			}
			if(($value = URLType::validate($value->value))->valid) {
				return $value;
			}
		}
		return Returner::invalid(static::Name);
	}
}