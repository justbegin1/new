<?php
namespace ExampleApp;
require_once '../../../vendor/autoload.php';

use Krishna\API\Config;
use Krishna\API\Debugger;
use Krishna\API\Server;

Config::$dev_mode = true;

Server::init();
Server::execute();