<?php

namespace Symm\Gisconverter\Exceptions;

class InvalidText extends CustomException
{
    public function __construct($decoder_name, $text = "")
    {
        $this->message =  "invalid text for decoder " . $decoder_name . ($text ? (": " . $text) : "");
    }
}
