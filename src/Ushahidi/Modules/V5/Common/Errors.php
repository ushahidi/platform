<?php

namespace Ushahidi\Modules\V5\Common;

use Exception;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Core\Exception\ValidatorException;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait Errors
{

    /**
     * Entity not found error
     * @param string $resource
     * @return void
     * @throws NotFoundException
     */
    public static function errorNotFound(string $resource, string $lookup): void
    {
        throw new NotFoundException(sprintf(
            'Could not locate any %s matching [%s]',
            $resource,
            $lookup
        ));
    }

     /**
     * validation not correct error
     * @param string $resource
     * @return void
     * @throws ValidatorException
     */
    public static function errorInvalidData(string $resource, array $errors): void
    {
        throw new ValidatorException(sprintf(
            'Failed to validate %s',
            $resource
        ), $errors);
    }

    /**
     * Db error
     * @param string $resource
     * @return void
     * @throws ValidatorException
     */
    public static function errorDB(\Exception $exception): void
    {
        // To Do create DB Exception
        throw $exception;
    }

    /**
     * undefined error
     * @param string $message
     * @return void
     * @throws HttpException
     */
    public static function error(string $message, Exception $exception = null): void
    {
        // To Do create DB Exception
        throw new HttpException($message, $exception);
    }
}
