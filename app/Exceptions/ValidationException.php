<?php

namespace Ushahidi\App\Exceptions;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Validation Exception
 */
class ValidationException extends UnprocessableEntityHttpException
{
    protected $errors = [];

    /**
     * Constructor.
     *
     * @param string     $message  The internal exception message
     * @param \Exception $previous The previous exception
     * @param int        $code     The internal exception code
     */
    public function __construct($message = null, \Exception $previous = null, Array $errors = null)
    {
        $this->errors = $errors ?: [];

        if (method_exists($previous, 'getErrors')) {
            $this->errors = $this->errors + $previous->getErrors();
        }

        parent::__construct($message, $previous, 0);
    }

    public function getErrors()
    {
        return $this->errors ?: [];
    }
}
