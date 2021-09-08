<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Returner;

class UnsignedNullType extends \KrishnaAPI\ParameterType\Abstract\MultiTypeParameter {
	const Consumes = 1;
	const Name = "int:{unsigned}|null";
	const Types = [UnsignedType::class, NullType::class];
}