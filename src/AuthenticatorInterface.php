<?php
namespace Krishna\API;

use Krishna\DataValidator\Returner;

interface AuthenticatorInterface {
	public static function authenticate(array $param) : Returner;
}