<?php

/* Copyright 2013, deviantART, Inc.
 * Licensed under 3-Clause BSD.
 * Refer to the LICENCES.txt file for details.
 * For latest version, see https://github.com/deviantART/ddt
 */

namespace DVNT;

class DDT
{

    /**
     * Default cookie options.
     * NOTE: cookie name must match ddt.js setting!
     */
    public static $defaults = array(
        'name' => 'ddt_watch',
        'expires' => 31557600, // one year, 365 * 24 * 60 * 60
        'path' => '/',
    );

    /**
     * Write the DDT watched channel list to a cookie.
     * @param  string $channels comma separated list
     * @param  array  $options  custom cookie options
     * @return bool
     */
    public static function writeCookie($channels = null, array $options = null)
    {
        if ($channels === null) {
            $channels = @$_GET['channels'];
        }

        $options = $options ? static::$defaults + $options : static::$defaults;

        // look for a comma-separated list of alphanumeric strings
        if ($channels && !preg_match('/^[a-zA-Z0-9,]+$/', trim($channels))) {
            return false;
        }

        // nothing tricky, set the cookie
        $expires = (int) $options['expires'] + time();

        // .current.tld instead of www.current.tld
        $server = '.' . implode('.', array_slice(explode('.', $_SERVER['SERVER_NAME']), -2));

        return (bool) setcookie($options['name'], $channels, $expires, $options['path'], $server);
    }

    /**
     * Write the DDT cookie and exit, serving a 1x1 transparent gif.
     * @param  string $channels comma separated list
     * @param  array  $options  custom cookie options
     * @return void
     */
    public static function writeAndExit($channels = null, array $options = null)
    {
        if (static::writeCookie($channels, $options)) {
            $status = 200;
        } else {
            $status = 400;
        }

        // send an empty 1x1 gif
        header('Content-Type: image/gif', true, $status);
        header('Cache-Control: nocache');

        exit(base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=='));
    }
}

DDT::writeAndExit();
