<?php
namespace KrishnaAPI\ParameterType;
use KrishnaAPI\Returner;

class URLNullType extends \KrishnaAPI\ParameterType\Abstract\MultiTypeParameter {
	const Consumes = 1;
	const Name = '';
	const Types = [NullType::class, URLType::class];
}