<?php
namespace KrishnaAPI\Abstract;

abstract class StaticOnly {
	final protected function __construct() {}
	final public static function __getStaticProperties() {
		$class = new \ReflectionClass(static::class);
		return $class->getStaticProperties();
	}
}