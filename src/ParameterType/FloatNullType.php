<?php
namespace KrishnaAPI\ParameterType;

class FloatNullType extends \KrishnaAPI\ParameterType\Abstract\MultiTypeParameter {
	const Consumes = 1;
	const Name = "float|null";
	const Types = [FloatType::class, NullType::class];
}