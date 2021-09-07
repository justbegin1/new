<?php
namespace KrishnaAPI;

final class Router extends Abstract\StaticOnly {
	public static function info() : array {
		$query_parts = $_GET;
		unset($query_parts['__url']);
		$json_post_parts = static::_json_post();
		$query_parts = array_merge($query_parts, $_POST, $json_post_parts);

		$ret = ['route' => ['string' => rtrim(urldecode($_GET['__url']), '/'), 'path' => []], 'query' => $query_parts];

		if(strcasecmp($_GET['__url'], 'index.php') === 0) {
			$ret['route'] = '';
			return $ret;
		}
		$ret['route']['path'] = explode('/', rtrim(urldecode($_GET['__url']), '/'));
		return $ret;
	}

	protected static function _json_post() : ?array {
		if(array_key_exists('CONTENT_TYPE', $_SERVER)) {
			$content_types = explode(';', $_SERVER['CONTENT_TYPE']);
			if(in_array('application/json', $content_types)) {
				$post = file_get_contents('php://input');
				$post = JSON::decode($post);
				if($post !== NULL) {
					return $post;
				}
			}
		}
		return [];
	}
}