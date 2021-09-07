<?php
namespace KrishnaAPI;

final class JSON extends Abstract\StaticOnly {
	public static function encode(mixed $object, bool $pretty = false) : string {
		$options = JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE | JSON_INVALID_UTF8_SUBSTITUTE;
		if($pretty) {
			$options |= JSON_PRETTY_PRINT;
		}
		$out = json_encode($object, $options);
		return ($out === false) ? 'null' : $out;
	}

	public static function decode(string $json) { // Returns NULL on error
		return json_decode($json, true, flags: JSON_INVALID_UTF8_SUBSTITUTE);
	}
}