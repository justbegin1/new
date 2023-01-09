<?php
namespace ExampleApp;

use Krishna\API\AuthenticatorInterface;
use Krishna\API\Func;
use Krishna\DataValidator\Returner;

class Dummy implements AuthenticatorInterface {
	public function authenticate(array $param, string $functionName): Returner {
		if($param['allow'] ?? false) {
			return Returner::valid();
		}
		return Returner::invalid('AuthError: Not allowed');
	}
}

Func::add_authenticator(new \ExampleApp\Dummy);

Func::set_signature([
	'allow' => 'bool'
]);

Func::set_definition(function(array $params, string $funcname) {
	return 'Auth passed';
});