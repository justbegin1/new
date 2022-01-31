<?php
namespace Tester;
require_once '../../../vendor/autoload.php';

use Krishna\API\Config;
use Krishna\API\Server;

Config::$dev_mode = true;

Server::init();
Server::execute();