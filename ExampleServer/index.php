<?php
namespace Example;

use KrishnaAPI\API;
use KrishnaAPI\Config;

require_once 'vendor/autoload.php';

Config::$dev_mode = true;
API::init(
	APP_BASE_PATH: __DIR__,
	FUNC_BASE_PATH: __DIR__ . '/src/Function/'
);
API::execute();