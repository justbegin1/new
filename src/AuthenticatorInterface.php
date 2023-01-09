<?php
namespace Krishna\API;

use Krishna\DataValidator\Returner;

interface AuthenticatorInterface {
	public function authenticate(array $param, string $functionName) : Returner;
}