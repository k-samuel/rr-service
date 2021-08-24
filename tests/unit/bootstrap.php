<?php

define('ROOT_PATH', dirname(__FILE__, 3) . '/');
$config = require ROOT_PATH . 'config/common.php';
$config['config_path'] = ROOT_PATH . 'config/test/';

include ROOT_PATH . "vendor/autoload.php";