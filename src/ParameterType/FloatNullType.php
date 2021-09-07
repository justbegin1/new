<?php
namespace KrishnaAPI\ParameterType;

class FloatNullType extends \Krishna\API\Extendable\MultiTypeParameter {
	const Consumes = 1;
	const Name = "Float or Null";
	const Types = [FloatType::class, NullType::class];
}