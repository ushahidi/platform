<?php

namespace Symm\Gisconverter\Exceptions;

class UnimplementedMethod extends Unimplemented
{
    public function __construct($method, $class)
    {
        $this->message = "method {$this->class}::{$this->method}";
    }
}
