<?php
namespace Krishna\API;
enum StatusType : int {
	case OK = 0;
	case INVALID_REQ = 1;
	case UNAUTH_REQ = 2;
	case EXEC_ERR = 3;
	case DEV_ERR = 4;
}