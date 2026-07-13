<?php

namespace Symm\Gisconverter\Exceptions;

class Unimplemented extends CustomException
{
    public function __construct($message)
    {
        $this->message = "unimplemented $message";
    }
}
