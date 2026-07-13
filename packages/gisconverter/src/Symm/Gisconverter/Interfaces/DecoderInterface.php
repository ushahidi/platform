<?php

namespace Symm\Gisconverter\Interfaces;

interface DecoderInterface
{
    /**
     * @param  string   $text
     * @return Geometry
     */
    public static function geomFromText($text);
}
