<?php
namespace KrishnaAPI;

use KrishnaAPI\Interface\Authenticator;
use KrishnaAPI\Interface\Parameter;
use KrishnaAPI\RequestPath as RP;

final class Func extends Abstract\StaticOnly {
	protected static
		$_func_def = null,
		$_auth = [],
		$_signature = [];

	public static function set_definition(callable $func) {
		self::$_func_def = $func;
	}
	public static function set_authenticator(Authenticator ...$authenticators) {
		self::$_auth = [...$authenticators];
	}
	protected static function load_func_file(string $func_basepath, string $function_name) {
		$filename = strtolower($function_name);
		$filename = str_replace('.', '/', ucwords($filename, '._'));
		$filename = $func_basepath . str_replace('_', '', $filename). '.php';
		if(file_exists($filename)) {
			require_once $filename;
		} else {
			API::error("Function '{$function_name}' not found", RESP_INVALID_ERR);
		}
	}
	protected static function _break_parameter_name(string $name) : array {
		if(str_starts_with($name, '?')) {
			return [false, substr($name, 1)];
		}
		return [true, $name];
	}
	protected static function _verify_signature_structure(array $para) {
		$ret = [];
		foreach($para as $key => $type) {
			list($req, $name) = static::_break_parameter_name($key);
			$item = [
				'req' => $req,
				'found' => false,
				'value' => null
			];
			if(is_string($type)) {
				$long_type_name = API::get_parameter_type_classname($type);
				if($long_type_name === null) {
					API::dev_call_error("'{$type}' needs to be string{classname} of a subclass of '" . Parameter::class . "'", __CLASS__ . '\\'. 'set_signature');
				}
				$item['type'] = $long_type_name;
			} elseif(is_array($type)) {
				$item['type'] = static::_verify_signature_structure($type);
			} else {
				API::dev_call_error('Invalid array structure used for function signature', __CLASS__ . '\\'. 'set_signature');
			}
			$ret[$name] = $item;
		}
		return $ret;
	}
	protected static function _single_parameter_directly_in_path() : ?array {
		$path_len = RP::count();
		if(count(static::$_signature) !== 1) {
			return null;
		}
		foreach(static::$_signature as $name => $info);
		$type = static::$_signature[$name]['type'];
		$consumes = $type::Consumes;
		if($path_len !== $consumes) {
			return null;
		}
		$value = RP::peek($consumes);
		return [$name => $value];
	}
	protected static function _multi_parameter_in_path() : array {
		$ret = [];
		while(RP::has_more()) {
			$name = RP::consume();
			if(!array_key_exists($name, static::$_signature)) {
				API::error("Unknown parameter: '{$name}'", RESP_INVALID_ERR);
			}
			$type = static::$_signature[$name]['type'];
			$consumes = $type::Consumes;
			if(!RP::has_more($consumes)) {
				API::error("Value of parameter '{$name}' is invalid. Expected type: {" . $type::Name . "}", RESP_INVALID_ERR);
			}
			$value = RP::peek($consumes);
			RP::consume_peeked();
			$ret[$name] = $value;
		}
		return $ret;
	}
	protected static function _match_value_with_signature(array $design, $values, string $previous = '') : array {
		foreach($design as $name => &$info) {
			if(array_key_exists($name, $values)) {
				$type = $info['type'];
				if(is_string($type)) {
					$val = $type::verify($values[$name]);
					if($val->valid) {
						$info['found'] = true;
						$info['value'] = $val->value;
					} else {
						API::error("Value of parameter '{$previous}{$name}' is invalid. Expected type: {" . $val->value . "}", RESP_INVALID_ERR);
					}
				} elseif(is_array($type) && is_array($values[$name])) {
					if(count($type) === 1 && array_key_exists(0, $type)) {
						$type = $type[0]['type'];
						$vals = [];
						foreach ($values[$name] as $item) {
							$val = $type::verify($item);
							if($val->valid) {
								$vals[] = $val->value;
							} else {
								API::error("Value of parameter '{$previous}{$name}' is invalid. Expected type: {" . $val->value . "}", RESP_INVALID_ERR);
							}
						}
						$info['found'] = true;
						$info['value'] = $vals;
					} else {
						$vals = [];
						foreach (static::_match_value_with_signature($type, $values[$name], "{$name}.") as $k => $v) {
							$vals[$k] = $v['value'];
						}
						$info['value'] = $vals;
						$info['found'] = true;
					}
				} else {
					API::error("Value of parameter '{$previous}{$name}' is invalid. Expected type: {" . $type::Name . "}", RESP_INVALID_ERR);
				}
			} else {
				if($info['req']) {
					API::error("Parameter '{$previous}{$name}' is required", RESP_INVALID_ERR);
				}
			}
		}
		return $design;
	}
	protected static function _signature_to_params(array $design) : array {
		$param = [];
		foreach ($design as $name => $info) {
			if($info['found']) {
				$param[$name] = $info['value'];
			}
		}
		return $param;
	}
	protected static function _verify_parameters() {
		$from_path = static::_single_parameter_directly_in_path();
		if($from_path === null) {
			$from_path = static::_multi_parameter_in_path();
		}
		$query = Request::info()['query'];
		$query = array_merge($from_path, $query);
		static::$_signature = static::_match_value_with_signature(static::$_signature, $query);
		static::$_signature = static::_signature_to_params(static::$_signature);
	}
	public static function set_signature(array $parameters) {
		static::$_signature = static::_verify_signature_structure($parameters);
		static::_verify_parameters();
	}
	public static function execute(string $func_basepath, array $req) {
		$fname = $req['func'] ?? 'default';
		static::load_func_file($func_basepath, $fname);
		if(!is_callable(self::$_func_def)) {
			API::error("Function '{$fname}' has not been defined", RESP_DEVELOPER_ERR);
		}
		foreach (self::$_auth as $test) {
			$authorisation = $test::authenticate(static::$_signature);
			if(!$authorisation->valid) {
				API::error($authorisation->value, RESP_UNAUTH_ERR);
			}
		}
		return (self::$_func_def)(static::$_signature, $fname);
	}
}