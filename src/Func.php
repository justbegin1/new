<?php
namespace Krishna\API;

use Krishna\DataValidator\MultiLinedException;
use Krishna\DataValidator\ObjectHandler;
use Krishna\DataValidator\Validator;

final class Func {
	use StaticOnlyTrait;
	private static $definition = null;
	private static array $auth = [];
	private static $signature = null;

	public static function set_definition(callable $handler) {
		self::$definition = $handler;
	}
	public static function add_authenticator(AuthenticatorInterface ...$handlers) {
		foreach($handlers as $h) {
			self::$auth[] = $h;
		}
	}
	public static function set_signature(array $signature) {
		try {
			self::$signature = new Validator($signature, true);
			if(!is_a(self::$signature->struct, ObjectHandler::class)) {
				throw new MultiLinedException('Invalid signature; It has to be a object structure');
			}
		} catch(MultiLinedException $ex) {
			Server::error_from($ex->getInfo(), __METHOD__);
		}
	}
	private static function process_path_query() {
		if(!is_a(self::$signature, Validator::class)) {
			Server::process_path_query([]);
			return;
		}
		$sig_info = [];
		foreach(self::$signature->struct->list as $k => $v) {
			if(property_exists($v['handler'], 'types')) {
				$sig_info[$k] = TypeInfo::min_consume(...$v['handler']->types);
			}
		}
		Server::process_path_query($sig_info);
	}
	public static function execute(string $base_path, array &$request) {
		$func_file = $base_path . str_replace('_', '', str_replace('.', '/', ucwords(strtolower($request['func']), '._')));
		$func_file .= is_dir($func_file) ? ('/' . Config::$func_default_name . '.php') : '.php';
		if(is_readable($func_file)) {
			require_once $func_file;
		} else {
			if($request['func'] === Config::$func_default_name) {
				return 'You have reached the API server; Welcome';
			}
			Server::error("API '{$request['func']}' not found", StatusType::INVALID_REQ);
		}
		self::process_path_query();
		if(!is_callable(self::$definition)) {
			Server::error("Function '{$request['func']}' has not been defined", StatusType::DEV_ERR);
		}
		if(self::$signature === null) {
			return (self::$definition)([], $request['func']);
		}
		$query = self::$signature->validate($request['query']);
		if($query->valid) {
			return (self::$definition)($query->value, $request['func']);
		}
		Server::error($query->error->errors, StatusType::INVALID_REQ);
	}
}