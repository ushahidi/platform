<?php

namespace v4\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Laravel\Lumen\Routing\Controller as BaseController;

class V4Controller extends BaseController
{
    /**
     * @param null $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function make500($message = null)
    {
        return response()->json(
            [
                'error'   => 404,
                'message' => $message ?? 'Not found',
            ],
            500
        );
    }

    /**
     * @param null $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function make404($message = null)
    {
        return response()->json(
            [
                'error'   => 404,
                'message' => $message ?? 'Not found',
            ],
            404
        );
    }

    /**
     * @param $messages
     * @param string $type (can be entity or translation)
     * @return \Illuminate\Http\JsonResponse
     */
    public static function make422($messages, $type = 'entity')
    {
        return response()->json(
            [
                'error'   => 422,
                'messages' => $messages,
                'type' => $type
            ],
            422
        );
    }
}
