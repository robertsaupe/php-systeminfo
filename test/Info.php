<?php
define('LIB_DIR', dirname(__DIR__));
define('LIB_TEST_DIR', __DIR__);
include_once(LIB_DIR . '/vendor/autoload.php');

use robertsaupe\SystemInfo\Info;

var_dump(Info::getTotalSpace());
var_dump(Info::decodeSizeBinary(Info::getTotalSpace()));
var_dump(Info::decodeSizeDecimal(Info::getTotalSpace()));

var_dump(Info::getFreeSpace());
var_dump(Info::decodeSizeBinary(Info::getFreeSpace()));
var_dump(Info::decodeSizeDecimal(Info::getFreeSpace()));

var_dump(Info::getUsedSpace());
var_dump(Info::decodeSizeBinary(Info::getUsedSpace()));
var_dump(Info::decodeSizeDecimal(Info::getUsedSpace()));

var_dump(Info::getDirectorySize());
var_dump(Info::decodeSizeBinary(Info::getDirectorySize()));
var_dump(Info::decodeSizeDecimal(Info::getDirectorySize()));

var_dump(Info::getDirectorySizeExec());
var_dump(Info::decodeSizeBinary(Info::getDirectorySizeExec()));
var_dump(Info::decodeSizeDecimal(Info::getDirectorySizeExec()));

var_dump(Info::getDirectorySizeNative());
var_dump(Info::decodeSizeBinary(Info::getDirectorySizeNative()));
var_dump(Info::decodeSizeDecimal(Info::getDirectorySizeNative()));

var_dump(Info::getProcessUserID());
var_dump(Info::getProcessUserName());

print_r(Info::getEnv());

print_r(Info::getEnvironment());

print_r(Info::phpInfo());

print_r(Info::phpInfoExec());

?>