<?php

namespace Ushahidi\App\Http\Middleware;

use Closure;

class CheckForInvalidJSON
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request_method =  $_SERVER['REQUEST_METHOD'];
        $put_or_post = $request_method === 'POST' || $request_method === 'PUT';

        $data = json_decode($request->getContent());

        // Check for NULL not empty - since [] and {} will be empty but valid
        if ($data === null && $put_or_post) {
            // Get further error info
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    $error = 'No errors';
                    break;
                case JSON_ERROR_DEPTH:
                    $error = 'Maximum stack depth exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $error = 'Underflow or the modes mismatch';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $error = 'Unexpected control character found';
                    break;
                case JSON_ERROR_SYNTAX:
                    $error = 'Syntax error, malformed JSON';
                    break;
                case JSON_ERROR_UTF8:
                    $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                default:
                    $error = 'Unknown error';
                    break;
            }

            if (json_last_error() !== JSON_ERROR_NONE) {
                abort(422, $error);
            }
        }

        return $next($request);
    }
}
