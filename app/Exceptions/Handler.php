<?php

namespace Ushahidi\App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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
        IlluminateValidationException::class,
        LaravelOAuthServerException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Throwable $e)
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
    public function render($request, Throwable $e)
    {
        return parent::render($request, $e);
    }

    /**
     * Prepare a JSON response for the given exception.
     * If request asks for JSON then we return the error as JSON
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function prepareJsonResponse($request, Throwable $e)
    {
        if ($e instanceof HttpExceptionInterface) {
            $headers = $e->getHeaders();
            $statusCode = $e->getStatusCode();
        } elseif ($e instanceof LeagueOAuthServerException) {
            $headers = $e->getHttpHeaders();
            // $authScheme = \strpos($request->header('Authorization')[0], 'Bearer') === 0 ? 'Bearer' : 'Basic';
            // $headers['WWW-Authenticate'] = $authScheme . ' realm="OAuth"';
            $statusCode = $e->getHttpStatusCode();
        } else {
            $headers = [];
            $statusCode = 500;
        }

        $defaultError = [
            'status' => $statusCode
        ];

        if ($message = $e->getMessage()) {
            $defaultError['message'] = is_object($message) ? $message->toArray() : $message;
        }

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

        return $response;
    }
}
