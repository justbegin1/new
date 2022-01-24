<?php
namespace Krishna\API;

final class TypeInfo {
	use StaticOnlyTrait;
	private static array $consume_info = [];
	public static function get(string $type) {
		if(array_key_exists($type, self::$consume_info)) {
			return ['class' => $type, 'consume' => self::$consume_info[$type]];
		}
		$consume = (defined("{$type}::Consume")) ? constant("{$type}::Consume") : 1;
		self::$consume_info[$type] = $consume;

		return ['class' => $type, 'consume' => $consume];
	}
	public static function min_consume(string ...$types) : int {
		$min = 100000;
		foreach ($types as $t) {
			['consume' => $consume] = self::get($t);
			if($consume < $min) {
				$min = $consume;
			}
		}
		return $min;
	}
}