<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Returner;

class Str64StrType extends \KrishnaAPI\ParameterType\Abstract\MultiTypeParameter {
	const Consumes = 1;
	const Name = "string|string:{Base64URL encoded}";
	const Types = [String64Type::class, StringType::class];
}