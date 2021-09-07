<?php
namespace KrishnaAPI\Interface;

interface Authenticator {
	public static function authenticate(array $param) : \KrishnaAPI\Returner;
}