<?php
namespace KrishnaAPI\ParameterType\Abstract;

use KrishnaAPI\API;
use KrishnaAPI\Returner;

use const KrishnaAPI\RESP_DEVELOPER_ERR;

abstract class MultiTypeParameter implements \KrishnaAPI\Interface\Parameter {
	const Consumes = 1;
	const Name = 'mixed';
	const Types = [];

	public static function verify($value): Returner {
		if(!is_array(static::Types) || count(static::Types) === 0) {
			API::error('{const Types} not properly initialised in {' . static::class . '}', RESP_DEVELOPER_ERR);
		}
		$msg = [];
		foreach(static::Types as $type) {
			$type = API::get_parameter_type_classname($type);
			if($type === null) {
				API::error("Unknown type '{$type}' used in {" . static::class . "} ");
			}
			$eval = $type::verify($value);
			if($eval->valid) {
				return $eval;
			} else {
				$msg[] = $type::Name;
			}
		}
		return Returner::invalid(static::Name . ':{' . implode('|', $msg) . '}');
	}
}