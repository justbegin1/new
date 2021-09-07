<?php
namespace KrishnaAPI\ParameterType;

class ArrayNullType extends \KrishnaAPI\ParameterType\Abstract\MultiTypeParameter {
	const Consumes = 1;
	const Name = "array|null";
	const Types = [ArrayType::class, NullType::class];
}