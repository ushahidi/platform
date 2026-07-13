<?php

namespace Symm\Gisconverter\Exceptions;

class InvalidFeature extends CustomException
{
    public function __construct($decoder_name, $text = "")
    {
        $this->message =  "invalid feature for decoder $decoder_name" . ($text ? ": $text" : "");
    }
}
