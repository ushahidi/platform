<?php

namespace Ushahidi\App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        // If request asks for JSON then we return the error as JSON
        if ($request->ajax() || $request->wantsJson()) {

            $statusCode = 500;
            $headers = [];

            if ($e instanceof HttpExceptionInterface) {
                $statusCode = $e->getStatusCode();
                $headers = $e->getHeaders();
            }

            $error = [
                'status' => $statusCode
            ];

            $message = $e->getMessage();
            if (is_object($message)) { $message = $message->toArray(); }

            if ($message) {
                $error['message'] = $message;
            }

            return response()->json([
                'errors' => [$error]
            ], $statusCode, $headers);
        }

        return parent::render($request, $e);
    }
}
