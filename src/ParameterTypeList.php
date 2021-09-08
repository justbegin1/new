<?php
namespace KrishnaAPI;

final class ParameterTypeList extends \KrishnaAPI\Abstract\StaticOnly {
	const
	Any				= '\\KrishnaAPI\\ParameterType\\AnyType',
// NUMBERS
	Int				= '\\KrishnaAPI\\ParameterType\\IntType',
	IntNull			= '\\KrishnaAPI\\ParameterType\\IntNullType',
	Float			= '\\KrishnaAPI\\ParameterType\\FloatType',
	FloatNull		= '\\KrishnaAPI\\ParameterType\\FloatNullType',
	Unsigned		= '\\KrishnaAPI\\ParameterType\\UnsignedType',
	UnsignedNull	= '\\KrishnaAPI\\ParameterType\\UnsignedNullType',
	Hex				= '\\KrishnaAPI\\ParameterType\\HexType',
	Number			= '\\KrishnaAPI\\ParameterType\\NumberType',
	NumberNull		= '\\KrishnaAPI\\ParameterType\\NumberNullType',

// BOOLEAN
	Bool			= '\\KrishnaAPI\\ParameterType\\BoolType',
	BoolNull		= '\\KrishnaAPI\\ParameterType\\BoolNullType',

// STRING
	String			= '\\KrishnaAPI\\ParameterType\\StringType',
	StringNull		= '\\KrishnaAPI\\ParameterType\\StringNullType',
	String64		= '\\KrishnaAPI\\ParameterType\\String64Type',
	String64Null	= '\\KrishnaAPI\\ParameterType\\String64NullType',
	Str64Str		= '\\KrishnaAPI\\ParameterType\\Str64StrType',

// ARRAY
	Array			= '\\KrishnaAPI\\ParameterType\\ArrayType',
	ArrayNull		= '\\KrishnaAPI\\ParameterType\\ArrayNullType',

// OTHERS	
	Null			= '\\KrishnaAPI\\ParameterType\\NullType',

	Flag			= '\\KrishnaAPI\\ParameterType\\FlagType',

	URL				= '\\KrishnaAPI\\ParameterType\\URLType',
	URLNull			= '\\KrishnaAPI\\ParameterType\\URLNullType',
	URL64			= '\\KrishnaAPI\\ParameterType\\URL64Type',

	Email			= '\\KrishnaAPI\\ParameterType\\EmailType',

	IP				= '\\KrishnaAPI\\ParameterType\\IPType',
	MAC				= '\\KrishnaAPI\\ParameterType\\MACType',

	TimeStamp		= '\\KrishnaAPI\\ParameterType\\TimeStampType',
	TimeStampNull	= '\\KrishnaAPI\\ParameterType\\TimeStampNullType';
}