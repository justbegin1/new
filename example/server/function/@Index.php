<?php

use Krishna\API\Func;

Func::set_definition(function(array $params, string $funcname) {
	\Krishna\API\Debugger::dump('test', 'test debug message');
	return 'This is default response';
});