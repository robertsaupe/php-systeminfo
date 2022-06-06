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

use function posix_getpwuid;
use function posix_geteuid;
use function file_exists;
use function realpath;
use function is_object;
use function disk_total_space;
use function disk_free_space;
use function getenv;
use function ob_start;
use function ob_get_clean;
use function phpinfo;
use function exec;
use function is_array;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;
use SplFileInfo;

class Info {

    use NotInstantiable;

    public static function getProcessUserID(): int {
        return posix_geteuid();
    }

    public static function getProcessUserName(): ?string {
        $processUser = posix_getpwuid(self::getProcessUserID());
        /** @phpstan-ignore-next-line */
        if (isset($processUser['name'])) {
            return $processUser['name'];
        } else {
            return null;
        }
    }

    /**
     * notice: https://en.wikipedia.org/wiki/Binary_prefix
     */
    public static function decodeSizeBinary(int|false $bytes): string {
        if (($bytes === false)) return 'not available';
        $types = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
        for($i = 0; $bytes >= 1024 && $i < (count($types)-1); $bytes /= 1024, $i++);
        return(round($bytes, 2) . ' ' . $types[$i]);
    }

    /**
     * notice: https://en.wikipedia.org/wiki/Binary_prefix
     */
    public static function decodeSizeDecimal(int|false $bytes): string {
        if (($bytes === false)) return 'not available';
        $types = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        for($i = 0; $bytes >= 1000 && $i < (count($types)-1); $bytes /= 1000, $i++);
        return(round($bytes, 2) . ' ' . $types[$i]);
    }

    /**
     * same as decodeSizeBinary
     */
    public static function decodeSize(int|false $bytes): string {
        return self::decodeSizeBinary($bytes);
    }

    /**
     * use getDirectorySizeExec if available, otherwise use getDirectorySizeNative
     */
    public static function getDirectorySize(string $path = '.'): int|false {
        return (self::getDirectorySizeExec($path) !== false ? self::getDirectorySizeExec($path) : self::getDirectorySizeNative($path));
    }

    /**
     * native php function to get directory size
     */
    public static function getDirectorySizeNative(string $path = '.'): int|false {
        $bytes = 0;
        $path = realpath($path);
        /** @phpstan-ignore-next-line */
        if($path !== false && $path != '' && file_exists($path)) {
            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object) {
                if (is_object($object) && $object instanceof SplFileInfo) {
                    $bytes += $object->getSize();
                }
            }
            return $bytes;
        } else {
            return false;
        }
    }

    /**
     * best accuracy, but required php exec function and command tools (du and awk)
     */
    public static function getDirectorySizeExec(string $path = '.'): int|false {
        $path = realpath($path);
        /** @phpstan-ignore-next-line */
        if(Check::execAvailable() && $path !== false && $path != '' && file_exists($path)) {
            $bytes = @exec('du -b '. $path .' | awk "{print $1}"');
            $bytes = intval($bytes);
            if ($bytes == 4096) $bytes = 0;
            return $bytes;
        } else {
            return false;
        }
    }

    public static function getTotalSpace(string $path = '.'): int|false {
        $bytes = disk_total_space($path);
        return $bytes === false ? false : intval($bytes);
    }

    public static function getFreeSpace(string $path = '.'): int|false {
        $bytes = disk_free_space($path);
        return $bytes === false ? false : intval($bytes);
    }

    public static function getUsedSpace(string $path = '.'): int|false {
        $totalBytes = self::getTotalSpace($path);
        $freeBytes = self::getFreeSpace($path);
        return $totalBytes === false || $freeBytes === false ? false : $totalBytes - $freeBytes;
    }

    /**
     * @return mixed[]
     */
    public static function phpInfo(): array {
        ob_start();
        phpinfo();
        /** @phpstan-ignore-next-line */
        $phpInfo = explode("\n", ob_get_clean());
        return $phpInfo;
    }

    /**
     * @return mixed[]|false
     */
    public static function phpInfoExec(string $phpBinPath = 'php'): array|false {
        if (!Check::execAvailable()) return false;
        $phpInfo = [];
        @exec($phpBinPath . ' -i', $phpInfo);
        return $phpInfo;
    }

    /**
     * @return string|mixed[]|false
     */
    public static function getEnv(): string|array|false {
        return getenv();
    }

    /**
     * @return mixed[]
     */
    public static function getEnvironment(): array {

        $serverEssentialArguments = array(
            'PHP_SELF',
            'argv',
            'argc',
            'GATEWAY_INTERFACE',
            'SERVER_ADDR',
            'SERVER_NAME',
            'SERVER_SOFTWARE',
            'SERVER_PROTOCOL',
            'REQUEST_METHOD',
            'REQUEST_TIME',
            'REQUEST_TIME_FLOAT',
            'QUERY_STRING',
            'DOCUMENT_ROOT',
            'HTTP_ACCEPT',
            'HTTP_ACCEPT_CHARSET',
            'HTTP_ACCEPT_ENCODING',
            'HTTP_ACCEPT_LANGUAGE',
            'HTTP_CONNECTION',
            'HTTP_HOST',
            'HTTP_REFERER',
            'HTTP_USER_AGENT',
            'HTTPS',
            'REMOTE_ADDR',
            'REMOTE_HOST',
            'REMOTE_PORT',
            'REMOTE_USER',
            'REDIRECT_REMOTE_USER',
            'SCRIPT_FILENAME',
            'SERVER_ADMIN',
            'SERVER_PORT',
            'SERVER_SIGNATURE',
            'PATH_TRANSLATED',
            'SCRIPT_NAME',
            'REQUEST_URI',
            'PHP_AUTH_DIGEST',
            'PHP_AUTH_USER',
            'PHP_AUTH_PW',
            'AUTH_TYPE',
            'PATH_INFO',
            'ORIG_PATH_INFO'
        );

        $serverEssentials = [];
        foreach ($serverEssentialArguments as $arg) {
            if (isset($_SERVER[$arg])) {
                $server_arg = (is_array($_SERVER[$arg]) ? implode(" ", $_SERVER[$arg]) : $_SERVER[$arg]);
                $serverEssentials[$arg] = $server_arg;
            } else {
                $serverEssentials[$arg] = false;
            }
        }

        $environment = array(
            '_server' => $_SERVER,
            'server' => $serverEssentials,
            'env' => $_ENV,
            'get' => $_GET,
            'post' => $_POST,
            'request' => $_REQUEST,
            'files' => $_FILES,
            'session' => (isset($_SESSION) ? $_SESSION : false),
            'cookie' => $_COOKIE,
            'user' => self::getProcessUserName(),
            'php' => array(
                'version' => PHP_VERSION,
                'major_version' => PHP_MAJOR_VERSION,
                'minor_version' => PHP_MINOR_VERSION,
                'release_version' => PHP_RELEASE_VERSION,
                'version_id' => PHP_VERSION_ID,
                'extra_version' => PHP_EXTRA_VERSION,
                'zts' => PHP_ZTS,
                'debug' => PHP_DEBUG,
                'maxpathlen' => PHP_MAXPATHLEN,
                'os' => PHP_OS,
                'os_family' => PHP_OS_FAMILY,
                'sapi' => PHP_SAPI,
                'eol' => PHP_EOL,
                'int_max' => PHP_INT_MAX,
                'int_min' => PHP_INT_MIN,
                'int_size' => PHP_INT_SIZE,
                'float_dig' => PHP_FLOAT_DIG,
                'float_epsilon' => PHP_FLOAT_EPSILON,
                'float_min' => PHP_FLOAT_MIN,
                'float_max' => PHP_FLOAT_MAX,
                'default_include_path' => DEFAULT_INCLUDE_PATH,
                'pear_install_dir' => PEAR_INSTALL_DIR,
                'pear_extension_dir' => PEAR_EXTENSION_DIR,
                'extension_dir' => PHP_EXTENSION_DIR,
                'prefix' => PHP_PREFIX,
                'bindir' => PHP_BINDIR,
                'binary' => PHP_BINARY,
                'mandir' => PHP_MANDIR,
                'libdir' => PHP_LIBDIR,
                'datadir' => PHP_DATADIR,
                'sysconfdir' => PHP_SYSCONFDIR,
                'localstatedir' => PHP_LOCALSTATEDIR,
                'config_file_path' => PHP_CONFIG_FILE_PATH,
                'config_file_scan_dir' => PHP_CONFIG_FILE_SCAN_DIR,
                'shlib_suffix' => PHP_SHLIB_SUFFIX,
                'fd_setsize' => PHP_FD_SETSIZE,
                'loaded_extensions' => get_loaded_extensions()
            ),
            'magic' => array(
                'dir' => __DIR__,
                'file' => __FILE__,
                'class' => __CLASS__,
                'method' => __METHOD__,
                'function' => __FUNCTION__,
                'namespace' => __NAMESPACE__
            )
        );

        return $environment;
    }

}

?>