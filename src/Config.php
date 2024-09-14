<?php
namespace Krishna\API;

final class Config {
	use \Krishna\Utilities\StaticOnlyTrait;
	// Flags
	public static bool
		$dev_mode = false,
		$allow_jsonp = false;
	
	// Environment Settings
	public static string
		$magic_query_prop = '@__url__@',
		$jsonp_keyword = '_jsonp_',
		$func_default_name = '@index',
		$func_common_name = '@all';

	// Headers
	public static bool
		$zlib = true,
		$disable_caching = true,
		$send_content_type = true;
	public static ?array $access_control_headers = [
		'Access-Control-Allow-Origin: *'
	];
	public static ?array $other_headers = null;
}