<?php

namespace Symm\Gisconverter\Exceptions;

class UnavailableResource extends CustomException
{
    public function __construct($resource)
    {
        $this->message = "unavailable resource: $resource";
    }
}
