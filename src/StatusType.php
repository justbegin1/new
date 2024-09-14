<?php
namespace Krishna\API;
enum StatusType : int {
	// case NET_ERR = -1; On client side
	case OK = 0;
	case INVALID_REQ = 1;
	case UNAUTH_REQ = 2;
	case EXEC_ERR = 3;
	case DEV_ERR = 4;
	// case JSON_ERR = 5; On client side

	public function description(): string {
		return match($this) {
			self::OK => 'OK',
			self::INVALID_REQ => 'Invalid_Request',
			self::UNAUTH_REQ => 'Unauthorized_Request',
			self::EXEC_ERR => 'Execution_Error',
			self::DEV_ERR => 'Dev_Error',
		};
	}
}