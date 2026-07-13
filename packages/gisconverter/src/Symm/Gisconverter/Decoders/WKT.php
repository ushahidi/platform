<?php

namespace Symm\Gisconverter\Decoders;
use Symm\Gisconverter\Geometry\Point;
use Symm\Gisconverter\Exceptions\InvalidText;

class WKT extends Decoder
{
    public static function geomFromText($text)
    {
        $ltext = strtolower($text);
        $type_pattern = '/\s*(\w+)\s*\(\s*(.*)\s*\)\s*$/';

        if (!preg_match($type_pattern, $ltext, $matches)) {
            throw new InvalidText(__CLASS__, $text);
        }

        foreach (array("Point", "MultiPoint", "LineString", "MultiLineString", "LinearRing",
                     "Polygon", "MultiPolygon", "GeometryCollection") as $wkt_type) {
            if (strtolower($wkt_type) == $matches[1]) {
                $type = $wkt_type;
                break;
            }
        }

        if (!isset($type)) {
            throw new InvalidText(__CLASS__, $text);
        }

        try {
            $components = call_user_func(array('static', 'parse' . $type), $matches[2]);
        } catch (InvalidText $e) {
            throw new InvalidText(__CLASS__, $text);
        } catch (\Exception $e) {
            throw $e;
        }

        $constructor = '\\Symm\\Gisconverter\\Geometry\\' . $type;

        return new $constructor($components);
    }

    protected static function parsePoint($str)
    {
        return preg_split('/\s+/', trim($str));
    }

    protected static function parseMultiPoint($str)
    {
        $str = trim($str);

        if (strlen($str) == 0) {
            return array();
        }

        return static::parseLineString($str);
    }

    protected static function parseLineString($str)
    {
        $components = array();

        foreach (preg_split('/,/', trim($str)) as $compstr) {
            $components[] = new Point(static::parsePoint($compstr));
        }

        return $components;
    }

    protected static function parseMultiLineString($str)
    {
        return static::parseCollection($str, "LineString");
    }

    protected static function parseLinearRing($str)
    {
        return static::parseLineString($str);
    }

    protected static function parsePolygon($str)
    {
        return static::parseCollection($str, "LinearRing");
    }

    protected static function parseMultiPolygon($str)
    {
        return static::parseCollection($str, "Polygon");
    }

    protected static function parseGeometryCollection($str)
    {
        $components = array();

        foreach (preg_split('/,\s*(?=[A-Za-z])/', trim($str)) as $compstr) {
            $components[] = static::geomFromText($compstr);
        }

        return $components;
    }

    protected static function parseCollection($str, $child_constructor)
    {
        $components = array();

        foreach (preg_split('/\)\s*,\s*\(/', trim($str)) as $compstr) {

            if (strlen($compstr) and $compstr[0] == '(') {
                $compstr = substr($compstr, 1);
            }

            if (strlen($compstr) and $compstr[strlen($compstr)-1] == ')') {
                $compstr = substr($compstr, 0, -1);
            }

            $childs = call_user_func(array('static', 'parse' . $child_constructor), $compstr);
            $constructor = 'Symm\\Gisconverter\\Geometry\\' . $child_constructor;
            $components[] = new $constructor($childs);
        }

        return $components;
    }
}
