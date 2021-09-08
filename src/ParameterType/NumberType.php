<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Returner;

class NumberType extends \KrishnaAPI\ParameterType\Abstract\MultiTypeParameter {
	const Consumes = 1;
	const Name = "int|float";
	const Types = [IntType::class, FloatType::class];
}