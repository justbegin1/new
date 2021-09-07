<?php
namespace KrishnaAPI;
use KrishnaAPI\Interface\Parameter;

/*Define API Constants*/
define('__START_TIME__', microtime(true));

/* API Response Types */
const RESP_DEBUG		= -1; /* Debug message */
const RESP_OK			= 0; /* Successfully executed */
const RESP_EXEC_ERR		= 1; /* Execution Error */
const RESP_INVALID_ERR	= 400; /* Invalid request */
const RESP_UNAUTH_ERR	= 401; /* UnAuthorised request */
const RESP_DEVELOPER_ERR	= 500; /* Developer error */

final class API extends Abstract\StaticOnly {
	protected static $_STARTTIME = null, $_BASE = [
		'LIB' => __DIR__,
		'APP' => null
	];
	protected static $_jsonp = null;
	protected static $_output = [], $_direct_exit = false, $_types_dict = [];

	public static function force_exit() {
		self::$_direct_exit = true;
		exit(0);
	}
	
	public static function get_parameter_type_classname(string $short_name) : ?string {
		if(array_key_exists($short_name, static::$_types_dict)) {
			return static::$_types_dict[$short_name];
		}
		if(is_subclass_of($short_name, Parameter::class)) {
			static::$_types_dict[$short_name] = $short_name;
			return $short_name;
		}
		$long_name = "\\KrishnaAPI\\ParameterType\\{$short_name}Type";
		if(is_subclass_of($long_name, Parameter::class)) {
			static::$_types_dict[$short_name] = $long_name;
			return $long_name;
		}
		return null;
	}

	protected static function _final_writer_() {
		if(!headers_sent()) {
			if((Config::$zlib === true)
			&& extension_loaded('zlib')
			&& isset($_SERVER['HTTP_ACCEPT_ENCODING'])
			&& substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
			&& (ini_get('output_handler') != 'ob_gzhandler')) {
				@ini_set('zlib.output_compression', 1);
			}
			if(Config::$no_cache_headers === true) {
				header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
				header('Expires: 0');
				header('Cache-Control: no-store, no-cache, must-revalidate');
				header('Cache-Control: post-check=0, pre-check=0', false);
				header('Pragma: no-cache');
			}
			if(Config::$access_control_allow_origin_all_header === true) {
				header('Access-Control-Allow-Origin: *');
			}
			if(Config::$content_type_header === true) {
				if(static::$_jsonp === NULL || !Config::$allow_jsonp) {
					header('Content-Type: application/json; charset=utf-8');
				} else {
					header('Content-type: application/javascript; charset=utf-8');
				}
			}
		}

		if(count(static::$_output) === 1) {
			static::$_output = static::$_output[0];
		}

		if(Config::$dev_mode) {
			static::$_output['exe_time'] = microtime(TRUE) - static::$_STARTTIME;
			static::$_output['mem_peak'] = memory_get_peak_usage();
		}

		$response = JSON::encode(static::$_output);

		if(static::$_jsonp !== NULL && Config::$allow_jsonp) {
			$response = static::$_jsonp . "({$response});";
		}
		echo $response;
	}
	public static function respond($value, bool $auto_exit = TRUE) {
		self::$_output[] = $value;
		if($auto_exit) {
			exit(0);
		}
	}

	public static function error($info, int $error_type = RESP_EXEC_ERR, bool $auto_exit = TRUE) {
		$response = ['status' => $error_type, 'res' => $info];
		if(!Config::$dev_mode) {
			switch($error_type) {
				case RESP_DEBUG:
					$response['res'] = 'Internal debug message';
					break;
				case RESP_DEVELOPER_ERR:
					$response['res'] = 'Internal server error';
					break;
			}
		}
		self::respond($response, $auto_exit);
	}
	public static function dev_call_error($msg, $func_name = null) {
		$info = Debugger::trace_call_point($func_name ?? (__CLASS__ . '\\' . __FUNCTION__));
		$info['msg'] = "Call to function [{$info['call_to']}]: {$msg}";
		unset($info['call_to']);
		API::error($info, RESP_DEVELOPER_ERR);
	}

	public static function init(string $APP_BASE_PATH, ?string $FUNC_BASE_PATH = null, ?array $config = null) {
		if(isset($GLOBALS['Krishna_API_Init_Called'])) {
			return;
		}
		$GLOBALS['Krishna_API_Init_Called'] = true;

		if($config !== null) {
			Config::overwrite($config);
		}

		if(Config::$disable_auto_error_reporting === true) {
			error_reporting(0);
		}
		
		static::$_STARTTIME = __START_TIME__;
		static::$_BASE['APP'] = $APP_BASE_PATH;
		static::$_BASE['FUNC'] = $FUNC_BASE_PATH ?? ($APP_BASE_PATH . '/Function/');
		
		set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
			$GLOBALS['Krishna_API_Error_Handler_Called'] = true;
			API::error([
				'error' => ['line' => $errline, 'file' => $errfile, 'msg' => $errstr],
				'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
			], RESP_DEVELOPER_ERR);
		}, E_ALL | E_STRICT);
		register_shutdown_function(function () {
			if(!self::$_direct_exit) {
				if(!isset($GLOBALS['Krishna_API_Error_Handler_Called'])) {
					$error = error_get_last();
					if($error !== NULL) {
						Config::$zlib = FALSE;
						API::error([
							'error' => ['line' => $error['line'], 'file' => $error['file'], 'msg' => $error['message']],
							'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
						], RESP_DEVELOPER_ERR, FALSE);
					}
				}
				self::_final_writer_();
			}
		});
	}
	public static function execute() {
		if(!isset($GLOBALS['Krishna_API_Init_Called'])) {
			self::init(__DIR__);
		}
		$req = Request::info();
		static::$_jsonp = $req['jsonp'];
		$response = Func::execute(static::$_BASE['FUNC'] ,$req);
		API::respond(['status' => RESP_OK, 'res' => $response]);
	}
}