<?php

namespace Ushahidi\App\PlatformVerifier;

use Ushahidi\App\Tools\OutputText;
use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class OAuth
{
    private static $NO_OAUTH_KEYS = "Required oAuth keys not found in storage/passport";
    private static $NO_OAUTH_KEYS_EXPLAINER = "Please run 'php artisan passport:keys' to create the keys";

    public function oauthKeysExist()
    {
        $publicKeyExists = file_exists(__DIR__ . "/../../storage/passport/oauth-public.key");
        $privateKeyExists = file_exists(__DIR__ . "/../../storage/passport/oauth-private.key");
        return $publicKeyExists and $privateKeyExists;
    }

    public function verifyRequirements($console = true)
    {
        $ok = "Good job! Your OAuth keys are in the right place.";
        $info = "We will check the environment file next.";
        $errors = [];
        $success = [];

        if (!$this->oauthKeysExist()) {
            array_push(
                $errors,
                ["message" => self::$NO_OAUTH_KEYS,
                "explainer" => self::$NO_OAUTH_KEYS_EXPLAINER],
                $console
            );
        }

        return !empty($errors) ?
            Respond::errorResponse($errors, $console) :
            Respond::successResponse($ok, $info, $console);
    }
}
