<?php
namespace Krishna\API;

use Krishna\DataValidator\TypeHandler;
use Krishna\Utilities\Debugger;
use Krishna\Utilities\JSON;

final class Server {
	use \Krishna\Utilities\StaticOnlyTrait;

	private static array
		$request = [
			'func' => null,
			'jsonp' => null,
			'path' => [],
			'query' => []
		],
		$debug = [];

	private static string $func_base_path;

	private static bool
		$direct_exit = false,
		$final_called = false,
		$init_flag = false;
	
	private static $custom_final_writer = null; // ?callable

	public static function set_custom_final_writer(?callable $func) : void {
		self::$custom_final_writer = $func;
	}

	public static function force_exit() : never {
		self::$direct_exit = true;
		exit(0);
	}
	public static function init(?string $func_base_path = null) :void {
		if(self::$init_flag) {
			return;
		}
		// Init flags and constants
		define(__NAMESPACE__ . '\\__START_TIME__', microtime(true));
		self::$init_flag = true;

		// Init error management
		error_reporting(0);
		set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
			error_clear_last();
			self::error([
				'line' => $errline,
				'file' => $errfile,
				'msg' => $errstr,
				'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
			], StatusType::DEV_ERR);
		}, E_ALL | E_STRICT);
		register_shutdown_function(function () {
			if(!self::$direct_exit) {
				if(($error = error_get_last()) !== null) {
					self::error([
						'line' => $error['line'],
						'file' => $error['file'],
						'msg' => $error['message'],
						'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
					], StatusType::DEV_ERR);
				}
				if(!self::$final_called) {
					self::final_writer('Nothing was executed');
				}
			}
		});
		
		// Init Debugger
		Debugger::$dumpper_callback = [Server::class, 'add_debug_msg'];

		// Init paths
		self::$func_base_path = realpath($func_base_path ?? (getcwd() . '/../function'));
		self::$func_base_path = (self::$func_base_path === false) ? (getcwd() . '/') : (self::$func_base_path . '/');

		// Init Custom Validator DataTypes
		TypeHandler::set_multiple_custom_type_class([
			'flag' => 'FlagType'
		], '\\Krishna\\API\\DataType');

		// Init request info
		$magicQueryProp = $_GET[Config::$magic_query_prop] ?? 'index.php';
		unset($_GET[Config::$magic_query_prop]);
		self::$request['path'] = (strcasecmp($magicQueryProp, 'index.php') === 0) ? [] : explode('/', rtrim(urldecode($magicQueryProp), '/'));
		self::$request['query'] = array_merge($_GET, $_POST, (function() {
				if(array_key_exists('CONTENT_TYPE', $_SERVER)) {
				$content_types = explode(';', $_SERVER['CONTENT_TYPE']);
				if(in_array('application/json', $content_types)) {
					$post = file_get_contents('php://input');
					$post = JSON::decode($post);
					if($post !== null) {
						return $post;
					}
				}
			}
			return [];
		})());
		$first = array_shift(self::$request['path']);
		if($first !== null && strcasecmp($first, Config::$jsonp_keyword) === 0) {
			$first = array_shift(self::$request['path']);
			self::$request['jsonp'] = ($first === null) ?  'jsonp' : preg_replace('/[^A-Z0-1_$\.]/i', '_', $first);
			$first = array_shift(self::$request['path']);
		}
		self::$request['func'] = $first ?? Config::$func_default_name;
	}
	public static function process_path_query(array $sig_info) {
		$path = new Consumable(self::$request['path']);
		$query = &self::$request['query'];

		// Single Parameter Directly in Path
		if(count($sig_info) === 1) {
			foreach($sig_info as $name => $consumes);
			if($consumes > 0 && $path->count === $consumes) {
				if($consumes === 1) {
					[$query[$name]] = $path->consume(1);
				} else {
					$query[$name] = $path->consume($consumes);
				}
			}
		}

		// Multiple Parameter in Path
		while($path->has_more) {
			[$key] = $path->consume();
			if(array_key_exists($key, $sig_info)) {
				$consumes = $sig_info[$key];
				if($consumes === 0) {
					$query[$key] = true;
				} elseif($path->count < $consumes) {
					Server::error("[{$key}]: Invalid value", StatusType::INVALID_REQ);
				} elseif($consumes === 1) {
					[$query[$key]] = $path->consume(1);
				} else {
					$query[$key] = $path->consume($consumes);
				}
			} else {
				Server::error("[{$key}]: Out of bound", StatusType::INVALID_REQ);
			}
		}
		unset(self::$request['path']);
	}
	private static function _default_final_writer_(mixed $value = null, StatusType $status = StatusType::OK) {
		if(!headers_sent()) {
			if(
				Config::$zlib === true
				&& extension_loaded('zlib')
				&& isset($_SERVER['HTTP_ACCEPT_ENCODING'])
				&& substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
				&& (ini_get('output_handler') != 'ob_gzhandler')
			) {
				@ini_set('zlib.output_compression', 1);
			}
			if(Config::$disable_caching === true) {
				header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
				header('Expires: 0');
				header('Cache-Control: no-store, no-cache, must-revalidate');
				header('Cache-Control: post-check=0, pre-check=0', false);
				header('Pragma: no-cache');
			}
			if(Config::$access_control_headers !== null) {
				foreach(Config::$access_control_headers as $h) {
					header($h);
				}
			}
			$is_jsonp = Config::$allow_jsonp && self::$request['jsonp'] !== null;
			if(Config::$send_content_type === true) {
				if($is_jsonp) {
					header('Content-type: application/javascript; charset=utf-8');
				} else {
					header('Content-Type: application/json; charset=utf-8');
				}				
			}
			if(Config::$other_headers !== null) {
				foreach(Config::$other_headers as $h) {
					header($h);
				}
			}
		}
		$response = [
			'status' => $status->value,
			'value' => $value
		];
		if(Config::$dev_mode) {
			$response['meta'] = [];
			if(defined(__NAMESPACE__ . '\\__START_TIME__')) {
				$response['meta']['exe_time'] = round(microtime(true) - constant(__NAMESPACE__ . '\\__START_TIME__'), 7);
			}
			$response['meta']['mem_peak'] = memory_get_peak_usage();
			if(count(self::$debug) > 0) {
				$response['debug'] = self::$debug;
			}
		}

		$response = JSON::encode($response);

		if($is_jsonp) {
			echo self::$request['jsonp'], '(', $response, ');';
		} else {
			echo $response;
		}
	}
	private static function final_writer(mixed $value = null, StatusType $status = StatusType::OK) : never {
		self::$final_called = true;
		if(self::$custom_final_writer === null) {
			self::_default_final_writer_($value, $status);
			exit(0);
		}
		if(!is_callable(self::$custom_final_writer)) {
			$status = StatusType::DEV_ERR;
			$value = 'Custom final writer is not callable';
			self::$debug[] = [
				'title' => 'Custom final writer',
				'value' => self::$custom_final_writer
			];
			self::_default_final_writer_($value, $status);
			exit(0);
		}
		
		$headers = [];
		$meta = null;

		if(!headers_sent()) {
			if(
				Config::$zlib === true
				&& extension_loaded('zlib')
				&& isset($_SERVER['HTTP_ACCEPT_ENCODING'])
				&& substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
				&& (ini_get('output_handler') != 'ob_gzhandler')
			) {
				@ini_set('zlib.output_compression', 1);
			}
		}
		if(Config::$disable_caching === true) {
			$headers[] = 'Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT';
			$headers[] = 'Expires: 0';
			$headers[] = 'Cache-Control: no-store, no-cache, must-revalidate';
			$headers[] = 'Cache-Control: post-check=0, pre-check=0';
			$headers[] = 'Pragma: no-cache';
		}
		if(Config::$access_control_headers !== null) {
			foreach(Config::$access_control_headers as $h) {
				$headers[] = $h;
			}
		}
		if(Config::$other_headers !== null) {
			foreach(Config::$other_headers as $h) {
				$headers[] = $h;
			}
		}
		if(Config::$dev_mode) {
			$meta = [];
			if(defined(__NAMESPACE__ . '\\__START_TIME__')) {
				$meta['exe_time'] = round(microtime(true) - constant(__NAMESPACE__ . '\\__START_TIME__'), 7);
			}
			$meta['mem_peak'] = memory_get_peak_usage();
			if(count(self::$debug) > 0) {
				$meta['debug'] = self::$debug;
			}
		}
		(self::$custom_final_writer)([
			'status' => $status,
			'value' => $value,
			'headers' => $headers,
			'meta' => $meta
		]);
		exit(0);
	}
	public static function error(mixed $info, StatusType $status = StatusType::EXEC_ERR) : never {
		if(!Config::$dev_mode) {
			switch($status) {
				// case StatusType::EXEC_ERR:
				// 	$info = 'Server execution error';
				// 	break;
				case StatusType::DEV_ERR:
					$info = 'Internal server error';
					break;
			}
		}
		self::final_writer($info, $status);
	}
	public static function add_debug_msg(mixed $info) : void {
		self::$debug[] = $info;
	}
	public static function error_from(mixed $msg, ?string $callpoint = null) : never {
		$info = Debugger::trace_call_point($callpoint ?? (__METHOD__));
		$info['msg'] = $msg;
		Server::error($info, StatusType::DEV_ERR);
	}
	public static function execute() : never {
		if(!self::$init_flag) {
			self::error_from('Server has not been initilised', __METHOD__);
		}
		Server::final_writer(Func::execute(self::$func_base_path, self::$request));
	}
}