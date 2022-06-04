<?php
define('LIB_DIR', dirname(__DIR__));
define('LIB_TEST_DIR', __DIR__);
include_once(LIB_DIR . '/vendor/autoload.php');

use robertsaupe\SystemInfo\OS;

var_dump(OS::getFamily());
var_dump(OS::getType());

var_dump(OS::getInfo());
var_dump(OS::getHostName());
var_dump(OS::getOSName());
var_dump(OS::getReleaseName());
var_dump(OS::getVersion());
var_dump(OS::getMachineType());

var_dump(OS::getLinuxInfo());

?>