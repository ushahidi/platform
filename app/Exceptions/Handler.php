<?php

namespace App\Exceptions;

use Throwable;
use Asm89\Stack\CorsService;
use Illuminate\Auth\AuthenticationException;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Core\Exception\ValidatorException;
use Ushahidi\Core\Exception\AuthorizerException;
use Ushahidi\Core\Exception\ThrottlingException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Laravel\Passport\Exceptions\OAuthServerException as LaravelOAuthServerException;
use League\OAuth2\Server\Exception\OAuthServerException as LeagueOAuthServerException;

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
        LaravelValidationException::class,
        LaravelOAuthServerException::class,
        LeagueOAuthServerException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     * @param \Throwable $exception
     *
     * @return void
     */
    public function report(Throwable $exception)
    {
        if ($this->shouldReport($exception) && app()->bound('sentry')) {
            \Sentry\Laravel\Facade::captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        // First handle some special cases
        if ($exception instanceof HttpResponseException) {
            // @todo check if we should still reformat this for json
            return $exception->getResponse();
        } elseif ($exception instanceof LaravelOAuthServerException) {
            return $exception->render($request);
        } elseif ($exception instanceof ModelNotFoundException) {
            $exception = new NotFoundHttpException($exception->getMessage(), $exception);
        } elseif ($exception instanceof AuthorizationException) {
            $exception = new HttpException(403, $exception->getMessage());
        } elseif ($exception instanceof AuthenticationException) {
            $exception = new HttpException(401, $exception->getMessage());
        } elseif ($exception instanceof LaravelValidationException && $exception->getResponse()) {
            // @todo check if we should still reformat this for json
            return $exception->getResponse();
        } elseif ($exception instanceof NotFoundException) {
            abort(404, $exception->getMessage());
        } elseif ($exception instanceof ValidatorException) {
            $exception = new ValidationException($exception->getMessage(), $exception);
        } elseif ($exception instanceof AuthorizerException) {
          //  If we don't have an Authorization header, return 401
            if (! $request->headers->has('Authorization')) {
                abort(
                    401,
                    'The request is missing an access token in either the Authorization header.',
                    ['www-authenticate' => 'Bearer realm="OAuth"']
                );
            } else {
                // Otherwise throw a 403
                abort(403, $exception->getMessage());
            }
        } elseif ($exception instanceof ThrottlingException) {
             abort(429, 'Too Many Requests');
        } elseif ($exception instanceof \InvalidArgumentException) {
            abort(400, 'Bad request: '.$exception->getMessage());
        }

        // If request asks for JSON then we return the error as JSON
        if ($request->ajax() || $request->wantsJson()) {
            $statusCode = $exception->status ?? 500;
            $headers = [];

            if ($exception instanceof HttpExceptionInterface) {
                $statusCode = $exception->getStatusCode();
                $headers = $exception->getHeaders();
            }

            $defaultError = [
                'status' => $statusCode,
            ];

            $message = $exception->getMessage();
            if ($message) {
                if (is_object($message)) {
                    $message = $message->toArray();
                }
                $defaultError['message'] = $message;
            }

            $errors = [];
            $errors[] = $defaultError;
            if ($exception instanceof ValidationException) {
                foreach ($exception->getErrors() as $key => $value) {
                    $errors[] = [
                        'status' => $statusCode,
                        'title' => $value,
                        'message' => $value,
                        'source' => [
                            'pointer' => '/' . $key,
                        ],
                    ];
                }
            }

            $response = response()->json([
                'errors' => $errors,
            ], $statusCode, $headers);

            // In the circumstance where an exception is raised
            // before the lumen request cycle reaches the middleware stage,
            // it is necessary to force the inclusion of the CORS headers.

            // This uses the app's CORS config.
            // This config must be loaded in the bootstrap process
            // before exception handlers are expected to be used.
            $options = config('cors');

            if (!$response->headers->has('Access-Control-Allow-Origin')) {
                // This CorsService relies on Asm89\Stack
                $cors = new CorsService($options);
                $response = $cors->addActualRequestHeaders($response, $request);
            }

            return $response;
        }
        return parent::render($request, $exception);
    }
}
