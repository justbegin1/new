<?php
namespace KrishnaAPI;

final class Returner {
	public $valid = false, $value = null;
	private function __construct(bool $valid, $value) {
		$this->valid = $valid;
		$this->value = $value;
	}
	public static function valid($value = true) : Returner {
		return new self(true, $value);
	}
	public static function invalid($reason = null) : Returner {
		return new self(false, $reason);
	}
}