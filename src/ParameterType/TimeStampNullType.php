<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Returner;

class TimeStampNullType extends \KrishnaAPI\ParameterType\Abstract\MultiTypeParameter {
	const Consumes = 1;
	const Name = 'string:{Timestamp}|null';
	const Types = [NullType::class, TimeStampType::class];
}