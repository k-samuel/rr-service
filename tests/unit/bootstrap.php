<?php
define('ROOT_PATH', dirname( dirname(dirname(__FILE__))) . '/');
$config = require ROOT_PATH . 'config/common.php';
$config['config_path'] = ROOT_PATH . 'config/test/';

include ROOT_PATH . "vendor/autoload.php";