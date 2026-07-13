<?php

namespace Symm\Gisconverter\Decoders;

use Symm\Gisconverter\Exceptions\InvalidText;
use Symm\Gisconverter\Geometry\Linestring;
use Symm\Gisconverter\Geometry\Point;
use Symm\Gisconverter\Geometry\LinearRing;
use Symm\Gisconverter\Geometry\Polygon;

class GeoJSON extends Decoder
{
    public static function geomFromText($text)
    {
        $ltext = strtolower($text);
        $obj = json_decode($ltext);

        if (is_null($obj)) {
            throw new InvalidText(__CLASS__, $text);
        }

        try {
            $geom = static::geomFromJson($obj);
        } catch (InvalidText $e) {
            throw new InvalidText(__CLASS__, $text);
        } catch (\Exception $e) {
            throw $e;
        }

        return $geom;
    }

    protected static function geomFromJson($json)
    {
        if (property_exists($json, "geometry") and is_object($json->geometry)) {
            return static::geomFromJson($json->geometry);
        }

        if (!property_exists($json, "type") or !is_string($json->type)) {
            throw new InvalidText(__CLASS__);
        }

        $geometryTypes = array(
            "Point",
            "Polygon",
            "MultiPoint",
            "LineString",
            "LinearRing",
            "MultiPolygon",
            "MultiLineString",
            "GeometryCollection"
        );

        foreach ($geometryTypes as $json_type) {
            if (strtolower($json_type) == $json->type) {
                $type = $json_type;
                break;
            }
        }

        if (!isset($type)) {
            throw new InvalidText(__CLASS__);
        }

        try {
            $components = call_user_func(array('static', 'parse'.$type), $json);
        } catch (InvalidText $e) {
            throw new InvalidText(__CLASS__);
        } catch (\Exception $e) {
            throw $e;
        }

        $constructor = 'Symm\\Gisconverter\\Geometry\\' . $type;

        return new $constructor($components);
    }

    protected static function parsePoint($json)
    {
        if (!property_exists($json, "coordinates") or !is_array($json->coordinates)) {
            throw new InvalidText(__CLASS__);
        }

        return $json->coordinates;
    }

    protected static function parseMultiPoint($json)
    {
        if (!property_exists($json, "coordinates") or !is_array($json->coordinates)) {
            throw new InvalidText(__CLASS__);
        }

        return array_map(
            function ($coords) {
                return new Point($coords);
            },
            $json->coordinates
        );
    }

    protected static function parseLineString($json)
    {
        return static::parseMultiPoint($json);
    }

    protected static function parseMultiLineString($json)
    {
        $components = array();

        if (!property_exists($json, "coordinates") or !is_array($json->coordinates)) {
            throw new InvalidText(__CLASS__);
        }

        foreach ($json->coordinates as $coordinates) {
            $linecomp = array();

            foreach ($coordinates as $coordinates) {
                $linecomp[] = new Point($coordinates);
            }

            $components[] = new LineString($linecomp);
        }

        return $components;
    }

    protected static function parseLinearRing($json)
    {
        return static::parseMultiPoint($json);
    }

    protected static function parsePolygon($json)
    {
        $components = array();

        if (!property_exists($json, "coordinates") or !is_array($json->coordinates)) {
            throw new InvalidText(__CLASS__);
        }

        foreach ($json->coordinates as $coordinates) {
            $ringcomp = array();

            foreach ($coordinates as $coordinates) {
                $ringcomp[] = new Point($coordinates);
            }

            $components[] = new LinearRing($ringcomp);
        }

        return $components;
    }

    protected static function parseMultiPolygon($json)
    {
        $components = array();

        if (!property_exists($json, "coordinates") or !is_array($json->coordinates)) {
            throw new InvalidText(__CLASS__);
        }
        foreach ($json->coordinates as $coordinates) {
            $polycomp = array();

            foreach ($coordinates as $coordinates) {
                $ringcomp = array();

                foreach ($coordinates as $coordinates) {
                    $ringcomp[] = new Point($coordinates);
                }

                $polycomp[] = new LinearRing($ringcomp);
            }

            $components[] = new Polygon($polycomp);
        }

        return $components;
    }

    protected static function parseGeometryCollection($json)
    {
        if (!property_exists($json, "geometries") or !is_array($json->geometries)) {
            throw new InvalidText(__CLASS__);
        }

        $components = array();

        foreach ($json->geometries as $geometry) {
            $components[] = static::geomFromJson($geometry);
        }

        return $components;
    }
}
