<?php

declare(strict_types=1);

/*
 * This file is part of the robertsaupe/php-systeminfo package.
 *
 * (c) Robert Saupe <mail@robertsaupe.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace robertsaupe\SystemInfo;

use function preg_match;
use function class_implements;
use function php_sapi_name;
use function array_key_exists;
use function function_exists;
use function strpos;
use function count;
use function ini_get;
use function exec;
use function extension_loaded;

class Check {

    use NotInstantiable;

    public static function isSSL(): bool {
        if (!empty( $_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            return true;
        } else if (!empty( $_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            return true;
        } else {
            return false;
        }
    }

    public static function isInsecure(): bool {
        return !self::isSSL();
    }

    public static function isSecure(): bool {
        return self::isSSL();
    }

    public static function isHTTP(): bool {
        return !self::isSSL();
    }

    public static function isHTTPS(): bool {
        return self::isSSL();
    }

    public static function isMail(string $mail): bool {
        if (preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/', $mail)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isText(string $text): bool {
        if (preg_match('/[^a-zA-Z0-9._-]/i', $text)) {
            return false;
        } else {
            return true;
        }
    }

    public static function classImplements(string $className, string $interfaceName): bool {
        $interfaces = class_implements($className);
        return isset($interfaces[$interfaceName]) ? true : false;
    }

    public static function isCli(): bool {
        if (defined('STDIN')) {
            return true;
        } else if (php_sapi_name() === 'cli') {
            return true;
        } else if (array_key_exists('SHELL', $_ENV)) {
            return true;
        } else if (empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0) {
            return true;
        } else if (!array_key_exists('REQUEST_METHOD', $_SERVER)) {
            return true;
        } else {
            return false;
        }
    }

    public static function execAvailable(): bool {
        if (function_exists('exec') && strpos(@ini_get('disable_functions'), 'exec') === false && @exec('echo EXEC') == 'EXEC') {
            return true;
        } else {
            return false;
        }
    }

    public static function shellexecAvailable(): bool {
        if (function_exists('shell_exec') && strpos(@ini_get('disable_functions'), 'shell_exec') === false && strpos(@shell_exec('echo EXEC'), 'EXEC') !== false) {
            return true;
        } else {
            return false;
        }
    }

    public static function popenAvailable(): bool {
        if (function_exists('popen') && strpos(@ini_get('disable_functions'), 'popen') === false) {
            $handle = @popen('echo EXEC', 'r');
            $read = fread($handle, 4);
            pclose($handle);
            return (strpos($read, 'EXEC') !== false) ? true : false;
        } else {
            return false;
        }
    }

    public static function programAvailable($programName): bool {
        if (@exec('command -v ' . $programName . ' >/dev/null 2>&1 || { echo >&1 "false";}') == 'false') {
            return false;
        } else {
            return true;
        }
    }

    public static function extensionAvailable($extensionName): bool {
        return extension_loaded($extensionName);
    }

}

?>