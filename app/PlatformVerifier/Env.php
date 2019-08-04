<?php

namespace Ushahidi\App\PlatformVerifier;

use Ushahidi\App\Tools\OutputText;
use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class Env
{
    private static $NO_ENV = "No environment file found. Please copy the .env.example file to create a new .env file.";
    private static $REQUIRED_ENV_KEYS = [
        "DB_CONNECTION" => "Please set `DB_CONNECTION=mysql` in the .env file.",
        "DB_HOST" => "Please set the address of your database in the DB_HOST key",
        "DB_PORT" => "Please set the port of your database in the DB_PORT key",
        "DB_DATABASE" => "Please set the name of your database in the DB_DATABASE key",
        "DB_USERNAME" => "Please set the username to connect to your database in the DB_USERNAME key",
        "DB_PASSWORD" => "Please set the password to connect to your database in the DB_PASSWORD key",
        "CACHE_DRIVER" => "Please set the CACHE_DRIVER according to your environment." .
                "See https://laravel.com/docs/5.8/cache#driver-prerequisites for more information on cache drivers.",
        "QUEUE_DRIVER" => "Please set the QUEUE_DRIVER according to your environment." .
                "See https://laravel.com/docs/5.8/queues for more information on queue drivers.",
    ];

    public static function verifyRequirements()
    {
        // load DotEnv for this script
        (new \Dotenv\Dotenv(__DIR__."/../../"))->load();

        if (!file_exists(__DIR__ . "/../../.env")) {
            echo OutputText::error(self::$NO_ENV);
            die;
        }
        $failures = false;
        foreach (self::$REQUIRED_ENV_KEYS as $key => $value) {
            if (!getenv($key)) {
                $failures = true;
                echo OutputText::error("$key is missing from your .env file." . PHP_EOL . $value);
            }
        }
        if (!$failures) {
            echo OutputText::success("Good job! you have configured your .ENV file with all the required keys.");
            echo OutputText::info("We will check the database connectivity next.");
        }
    }
}
