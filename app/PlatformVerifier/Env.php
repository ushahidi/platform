<?php

namespace Ushahidi\App\PlatformVerifier;

use Ushahidi\App\Tools\OutputText;
use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class Env
{
    private static $NO_ENV = "Required environment variables missing and no environment file found.";
    private static $NO_ENV_EXPLAINER = "Please copy the '.env.example' file into a file named '.env' " .
                                       "and set your missing variables.";
    private static $REQUIRED_ENV_KEYS = [
        "DB_CONNECTION" => "Please set `DB_CONNECTION=mysql` in the environment or .env file.",
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

    public function envExists()
    {
        return file_exists(__DIR__ . "/../../.env");
    }

    public function isMissingEnvKey($key)
    {
        return !getenv($key);
    }
    public function verifyRequirements($console = true)
    {
        $ok = "Good job! you have configured your system environment and/or .env file with all the required keys.";
        $info = "We will check the database connectivity next.";
        $errors = [];
        $success = [];

        if ($this->envExists()) {
            // load DotEnv for this script
            (new \Dotenv\Dotenv(__DIR__."/../../"))->load();
        }

        $failures = false;
        foreach (self::$REQUIRED_ENV_KEYS as $key => $value) {
            if ($this->isMissingEnvKey($key)) {
                $failures = true;
                $message = [
                    "message" => "$key is missing in the environment or .env file.",
                    "explainer" => $value
                ];
                array_push($errors, $message);
            }
        }
        // If there have been errors and the .env file is missing, point out that creating it
        // is a convenient way of solving those errors
        if (!empty($errors) && !$this->envExists()) {
            array_push($errors, ["message" => self::$NO_ENV, "explainer" => self::$NO_ENV_EXPLAINER], $console);
        }
        return $failures ? Respond::errorResponse($errors, $console) : Respond::successResponse($ok, $info, $console);
    }
}
