<?php

namespace Symm\Gisconverter\Decoders;

use Symm\Gisconverter\Interfaces\DecoderInterface;
use Symm\Gisconverter\Exceptions\UnimplementedMethod;

abstract class Decoder implements DecoderInterface
{
    public static function geomFromText($text)
    {
        throw new UnimplementedMethod(__FUNCTION__, get_called_class());
    }
}
