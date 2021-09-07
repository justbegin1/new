<?php
namespace KrishnaAPI\ParameterType;

class BoolNullType extends \KrishnaAPI\ParameterType\Abstract\MultiTypeParameter {
	const Consumes = 1;
	const Name = "bool|null";
	const Types = [BoolType::class, NullType::class];
}