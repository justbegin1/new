<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Returner;

class String64NullType extends \KrishnaAPI\ParameterType\Abstract\MultiTypeParameter {
	const Consumes = 1;
	const Name = 'string:{Base64URL encoded}|null';
	const Types = [NullType::class, String64Type::class];
}