<?php

namespace Ushahidi\App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
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
        IlluminateValidationException::class,
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
        // First handle some special cases
        if ($e instanceof HttpResponseException) {
            // @todo check if we should still reformat this for json
            return $e->getResponse();
        } elseif ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        } elseif ($e instanceof AuthorizationException) {
            $e = new HttpException(403, $e->getMessage());
        } elseif ($e instanceof ValidationException && $e->getResponse()) {
            // @todo check if we should still reformat this for json
            return $e->getResponse();
        }

        // If request asks for JSON then we return the error as JSON
        if ($request->ajax() || $request->wantsJson()) {

            $statusCode = 500;
            $headers = [];

            if ($e instanceof HttpExceptionInterface) {
                $statusCode = $e->getStatusCode();
                $headers = $e->getHeaders();
            }

            $defaultError = [
                'status' => $statusCode
            ];

            $message = $e->getMessage();
            if ($message) {
                if (is_object($message)) {
                    $message = $message->toArray();
                }
                $defaultError['message'] = $message;
            }

            $errors = [];
            $errors[] = $defaultError;
            if ($e instanceof ValidationException) {
                foreach ($e->getErrors() as $key => $value) {
                    $errors[] = [
                        'status' => $statusCode,
                        'title' => $value,
                        'message' => $value,
                        'source' => [
                            'pointer' => "/" . $key
                        ]
                    ];
                }
            }

            return response()->json([
                'errors' => $errors
            ], $statusCode, $headers);
        }

        return parent::render($request, $e);
    }
}
