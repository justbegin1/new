<?php
namespace KrishnaAPI\ParameterType;

class IntNullType extends \KrishnaAPI\ParameterType\Abstract\MultiTypeParameter {
	const Consumes = 1;
	const Name = "int|null";
	const Types = [IntType::class, NullType::class];
}