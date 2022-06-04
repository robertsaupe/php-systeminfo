<?php
define('LIB_DIR', dirname(__DIR__));
define('LIB_TEST_DIR', __DIR__);
include_once(LIB_DIR . '/vendor/autoload.php');

use robertsaupe\SystemInfo\Check;

var_dump(Check::isSSL());

var_dump(Check::isText('abc'));
var_dump(Check::isText('abc@example.com'));

var_dump(Check::isMail('abc'));
var_dump(Check::isMail('abc@example.com'));

var_dump(Check::isCli());

var_dump(Check::execAvailable());
var_dump(Check::shellexecAvailable());
var_dump(Check::popenAvailable());

var_dump(Check::programAvailable('dd'));
var_dump(Check::programAvailable('programNotExists'));

var_dump(Check::extensionAvailable('json'));
var_dump(Check::extensionAvailable('extensionNotExists'));

include_once(LIB_TEST_DIR . '/res/Interface.php');
include_once(LIB_TEST_DIR . '/res/Class.php');
var_dump(Check::classImplements('Test', 'iTest'));
var_dump(Check::classImplements('Test', 'iTestNotExists'));

?>