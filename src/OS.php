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

use const PHP_OS_FAMILY;
use function shell_exec;
use function preg_match_all;
use function array_walk;
use function array_combine;
use function preg_replace;
use function str_replace;
use function strtolower;

class OS {

    use NotInstantiable;

    /**
     * @return PHP_OS_FAMILY
     */
    public static function getFamily(): string {
        return PHP_OS_FAMILY;
    }

    /**
     * The operating system family PHP was built for. Either of 'Windows', 'BSD', 'macOS', 'Solaris', 'Linux' or 'Unknown'.
     */
    public static function getType(): string {
        return match(self::getFamily()) {
            'Darwin' => 'macOS',
            default => self::getFamily()
        };
    }

    /**
     * @return php_uname('a');
     */
    public static function getInfo(): string {
        return php_uname('a');
    }

    /**
     * @return php_uname('n');
     */
    public static function getHostName(): string {
        return php_uname('n');
    }

    /**
     * @return php_uname('s');
     */
    public static function getOSName(): string {
        return php_uname('s');
    }

    /**
     * @return php_uname('r');
     */
    public static function getReleaseName(): string {
        return php_uname('r');
    }

    /**
     * @return php_uname('v');
     */
    public static function getVersion(): string {
        return php_uname('v');
    }

    /**
     * @return php_uname('m');
     */
    public static function getMachineType(): string {
        return php_uname('m');
    }

    /**
     * returns an array of linux informations from /etc/os-release
     *
     * @link https://stackoverflow.com/a/42397673
     */
    public static function getLinuxInfo(): ?array {
        if (!Check::shellexecAvailable() || !is_readable("/etc/os-release")) return null;

        $os         = shell_exec('cat /etc/os-release');
        $listIds    = preg_match_all('/.*=/', $os, $matchListIds);
        $listIds    = $matchListIds[0];

        $listVal    = preg_match_all('/=.*/', $os, $matchListVal);
        $listVal    = $matchListVal[0];

        array_walk($listIds, function(&$v, $k) {
            $v = strtolower(str_replace('=', '', $v));
        });

        array_walk($listVal, function(&$v, $k) {
            $v = preg_replace('/=|"/', '', $v);
        });

        return array_combine($listIds, $listVal);
    }

}

?>