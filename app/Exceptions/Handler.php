<?php

namespace Ushahidi\App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Illuminate\Auth\AuthenticationException;
use Asm89\Stack\CorsService;
use Illuminate\Http\Request;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        AuthenticationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        IlluminateValidationException::class,
        OAuthServerException::class,
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
        if (app()->bound('sentry') && $this->shouldReport($e)) {
            app('sentry')->captureException($e);
        }

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
        // @todo we should try app('request') first but we can't guarantee its been created
        $request = Request::capture();
        
        // First handle some special cases
        if ($e instanceof HttpResponseException) {
            // @todo check if we should still reformat this for json
            return $e->getResponse();
        } elseif ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        } elseif ($e instanceof AuthorizationException) {
            $e = new HttpException(403, $e->getMessage());
        } elseif ($e instanceof AuthenticationException) {
            $e = new HttpException(401, $e->getMessage());
        } elseif ($e instanceof IlluminateValidationException && $e->getResponse()) {
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

            $response = response()->json([
                'errors' => $errors
            ], $statusCode, $headers);

            // In the circumstance where an exception is raised
            // before the lumen request cycle reaches the middleware stage,
            // it is necessary to force the inclusion of the CORS headers.

            // This uses the app's CORS config.
            // This config must be loaded in the bootstrap process
            // before exception handlers are expected to be used.
            $options = config('cors');

            if (! $response->headers->has('Access-Control-Allow-Origin')) {
                // This CorsService relies on Asm89\Stack
                $cors = new CorsService($options);
                $response = $cors->addActualRequestHeaders($response, $request);
            }


            return $response;
        }

        return parent::render($request, $e);
    }
}
