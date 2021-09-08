<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Returner;

class StringNullType extends \KrishnaAPI\ParameterType\Abstract\MultiTypeParameter {
	const Consumes = 1;
	const Name = "string|null";
	const Types = [NullType::class, StringType::class];
}