<?php

namespace Ushahidi\App\PlatformVerifier;

use Ushahidi\App\Tools\OutputText;
use Composer\Script\Event;
use Composer\Installer\PackageEvent;

# Methods to check whether the installation debug mode is enabled , as well
# to create and delete the file that enables it
#
# This would be a nice as just commands in the composer.json file,
# but that wouldn't be portable across platforms
class DebugMode
{
    private static $SWITCH_FILE = "bootstrap/install_debug_mode.enabled";
    private static $SWITCH_FILE_PATH = __DIR__ . "/../../bootstrap/install_debug_mode.enabled";

    private static function getLastErrorMessage()
    {
        $error = error_get_last();
        if ($error === null) {
            return '[no PHP error]';
        } else {
            return $error['message'];
        }
    }

    public static function isEnabled()
    {
        return file_exists(self::$SWITCH_FILE_PATH) ||
            ($_ENV['USH_PLATFORM_INSTALL_DEBUG_MODE_ENABLED'] ?? null);
    }

    public static function enable()
    {
        if (!file_exists(self::$SWITCH_FILE_PATH)) {
            // create the file
            if (touch(self::$SWITCH_FILE_PATH)) {
                echo OutputText::success(self::$SWITCH_FILE . " created");
            } else {
                echo OutputText::error("Creating " . self::$SWITCH_FILE . ": " . self::getLastErrorMessage());
            }
        } else {
            echo OutputText::info("The file " . self::$SWITCH_FILE . " already exists: no action taken.");
        }
    }

    public static function disable()
    {
        if (file_exists(self::$SWITCH_FILE_PATH)) {
            // delete the file
            if (unlink(self::$SWITCH_FILE_PATH)) {
                echo OutputText::success(self::$SWITCH_FILE . " deleted");
            } else {
                echo OutputText::error("Deleting " . self::$SWITCH_FILE . ": " . self::getLastErrorMessage());
            }
        } else {
            echo OutputText::info("The file " . self::$SWITCH_FILE . " didn't exist: no action taken.");
        }
    }
}
