<?php
namespace Krishna\API\DataType;

use Krishna\DataValidator\Returner;

class Json64Type implements \Krishna\DataValidator\TypeInterface {
	use \Krishna\API\StaticOnlyTrait;
	const Name = 'string:{Base64URL encoded JSON}';

	public static function validate($value, bool $allow_null = false): Returner {
		if(($value = String64Type::validate($value, $allow_null))->valid) {
			if(($value = JsonType::validate($value->value, $allow_null))->valid) {
				return $value;
			}
		}
		return Returner::invalid(static::Name);
	}
}