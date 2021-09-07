<?php
namespace KrishnaAPI;

final class Request extends Abstract\StaticOnly {
	private static $_info = [
		'func' => 'default',
		'query' => [],
		'path' => [],
		'jsonp' => null
	], $loaded = FALSE;
	
	protected static function _init() {
		$route = Router::info();
		static::$_info['query'] = $route['query'];
		if($route['route'] !== '') {
			static::$_info['path'] = $route['route']['path'];
			$first = array_shift(static::$_info['path']);
			if(strcasecmp($first, '_jsonp_') === 0) {
				$first = array_shift(static::$_info['path']);
				if($first === null) {
					static::$_info['jsonp'] = 'jsonp';
				} else {
					static::$_info['jsonp'] = preg_replace('/[^A-Z0-1_$]/i', '_', $first);
				}
				$first = array_shift(static::$_info['path']);
			}
			if($first !== null) {
				static::$_info['func'] = $first;
			}
		}
		RequestPath::init(static::$_info['path']);
		static::$loaded = true;
	}
	public static function info () : array {
		if(!static::$loaded) {
			static::_init();
		}
		return static::$_info;
	}
}