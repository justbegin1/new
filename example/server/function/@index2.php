<?php

use Krishna\API\Func;

Func::set_definition(function(array $params, string $funcname) {
	\Krishna\Utilities\Debugger::dump('Test', 'Testing debug message');
	return 'This is default response';
});