<?php
namespace Krishna\API;

use Krishna\DataValidator\ComplexException;
use Krishna\DataValidator\ObjectHandler;
use Krishna\DataValidator\OutOfBoundAction;
use Krishna\DataValidator\Validator;

final class Func {
	use \Krishna\Utilities\StaticOnlyTrait;
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
	public static function set_signature(array $signature, OutOfBoundAction $on_out_of_bound = OutOfBoundAction::Trim) {
		try {
			self::$signature = new Validator($signature, $on_out_of_bound);
			if(!is_a(self::$signature->struct, ObjectHandler::class)) {
				throw new ComplexException('Invalid signature; It has to be a object structure');
			}
		} catch(ComplexException $ex) {
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
	private static function genCommonFuncPathFiles(string $base_path,  string $func_path, bool $is_dir): iterable {
		$common_file = Config::$func_common_name . '.php';
		$func_path = explode('/', $func_path);
		if(!$is_dir) {
			array_pop($func_path);
		}
		yield $base_path . $common_file;
		foreach($func_path as $p) {
			$base_path .= $p. '/';
			yield $base_path . $common_file;
		}
	}
	public static function execute(string $base_path, array &$request) {
		$func_path = str_replace(['_', '.'], ['', '/'], ucwords(strtolower($request['func']), '._'));

		$dir_path = $base_path . $func_path;
		
		$is_dir = is_dir($dir_path);
		
		$func_file = $dir_path . ($is_dir ? ('/' . Config::$func_default_name . '.php') : '.php');

		$is_readable = is_readable($func_file);

		// Check if function defined or at server home page
		if(!$is_readable && $request['func'] !== Config::$func_default_name) {
			Server::error("API '{$request['func']}' not found", StatusType::INVALID_REQ);
		}
		
		// load common files
		foreach(self::genCommonFuncPathFiles($base_path, $func_path, $is_dir) as $file) {
			if(is_readable($file)) {
				require_once $file;
			}
		}

		if($is_readable) {
			require_once $func_file;
		} else {
			// API home page default message
			return 'You have reached the API server; Welcome';
		}

		self::process_path_query();
		if(!is_callable(self::$definition)) {
			Server::error("Function '{$request['func']}' has not been defined", StatusType::DEV_ERR);
		}
		// Validate query
		$query = [];
		if(self::$signature !== null) {
			$query = self::$signature->validate($request['query']);
			if($query->valid) {
				$query = $query->value;
			} else {
				Server::error($query->error->errors, StatusType::INVALID_REQ);
			}
		}
		// Authentcate
		foreach(self::$auth as $handler) {
			$test = $handler->authenticate($query, $request['func']);
			if(!$test->valid) {
				Server::error($test->error, StatusType::UNAUTH_REQ);
			}
		}
		
		return (self::$definition)($query, $request['func']);
	}
}