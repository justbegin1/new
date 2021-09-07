<?php
namespace KrishnaAPI;

use stdClass;

final class Config extends Abstract\StaticOnly {
	public static
		$dev_mode = false,
		$allow_jsonp = true,
		$disable_auto_error_reporting = true,
		//Headers
		$no_cache_headers = true,
		$zlib = true,
		$content_type_header = true,
		$access_control_allow_origin_all_header = true,
		//Container for app developer defined configurations
		$app = [];
	
	public static function overwrite(array $config) {
		foreach($config as $key=>$val) {
			switch ($key) {
				case 'dev_mode':
					self::$dev_mode = $val;
					break;
				case 'allow_jsonp':
					self::$allow_jsonp = $val;
					break;
				case 'disable_auto_error_reporting':
					self::$disable_auto_error_reporting = $val;
					break;
				case 'no_cache_headers':
					self::$no_cache_headers = $val;
					break;
				case 'zlib':
					self::$zlib = $val;
					break;
				case 'content_type_header':
					self::$content_type_header = $val;
					break;
				case 'access_control_allow_origin_all_header':
					self::$access_control_allow_origin_all_header = $val;
					break;
				case 'app':
					self::$app = $val;
			}
		}
	}
}