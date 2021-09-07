<?php
namespace KrishnaAPI;

final class RequestPath extends Abstract\StaticOnly {
	protected static $_parts, $_length, $_pos = -1, $_peeked = 0;
	
	public static function init(array $parts) {
		static::$_parts = $parts;
		static::$_length = count(static::$_parts);
	}

	public static function count() : int {
		return static::$_length;
	}

	public static function has_more(int $count = 1) : bool {
		return (static::$_pos + $count) < static::$_length;
	}

	public static function consume(int $count = 1) {
		if((static::$_pos + $count) >= static::$_length) {
			$ret = array_slice(static::$_parts, ++static::$_pos);
			static::$_pos = static::$_length;
			return $ret;
		}
		switch(true) {
			case $count === 1:
				return static::$_parts[++static::$_pos];
			case $count > 1:
				$ret = array_slice(static::$_parts, ++static::$_pos, $count);
				static::$_pos += $count - 1;
				return $ret;
			default:
				return null;
		}
	}

	public static function peek(int $count = 1) {
		static::$_peeked = $count;
		if((static::$_pos + $count) >= static::$_length) {
			$ret = array_slice(static::$_parts, ++static::$_pos);
			return $ret;
		}
		switch(true) {
			case $count === 1:
				return static::$_parts[static::$_pos + 1];
			case $count > 1:
				$ret = array_slice(static::$_parts, static::$_pos + 1, $count);
				return $ret;
			default:
				return null;
		}
	}

	public static function consume_peeked() {
		static::$_pos += static::$_peeked;
	}
}