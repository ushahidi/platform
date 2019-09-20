<?php

namespace Ushahidi\App\PlatformVerifier;

use Ushahidi\App\Tools\OutputText;

class Respond
{
    public static function successResponse($ok, $info, $console)
    {
        if ($console) {
            echo OutputText::success($ok);
            echo OutputText::info($info);
        }
        return ["success" => [["message" => $ok, "explainer" => null]]];
    }

    public static function errorResponse($errors, $console)
    {
        if ($console) {
            foreach ($errors as $error) {
                echo OutputText::error($error["message"]);
                echo OutputText::error($error["explainer"]);
            }
        }
        return ["errors" => $errors];
    }
}
