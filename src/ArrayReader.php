<?php
namespace KrishnaAPI;
class ArrayReader {
	protected $_info;
	public function __construct(array $info) {
		$this->_info = $info;
	}
	public function __serialize() : array {
		return $this->_info;
	}
	public function __debugInfo() : array {
		return $this->_info;
	}
	public function __get(string $key) {
		$keys = explode('.', $key);
		$ret = $this->_info;
		foreach($keys as $k) {
			if(array_key_exists($k, $ret)) {
				$ret = $ret[$k];
			} else {
				return null;
			}
		}
		return $ret;
	}
}