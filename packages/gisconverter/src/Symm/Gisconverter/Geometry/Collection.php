<?php

namespace Symm\Gisconverter\Geometry;

abstract class Collection extends Geometry
{
    protected $components;

    public function __get($property)
    {
        if ($property == "components") {
            return $this->components;
        } else {
            throw new \Exception("Undefined property");
        }
    }

    public function toWKT()
    {
        $recursiveWKT = function ($geom) use (&$recursiveWKT) {
            if ($geom instanceof Point) {
                return "{$geom->lon} {$geom->lat}";
            } else {
                return "(" . implode(',', array_map($recursiveWKT, $geom->components)). ")";
            }
        };

        return strtoupper(static::name) . call_user_func($recursiveWKT, $this);
    }

    public function toGeoArray()
    {
        $recurviseJSON = function ($geom) use (&$recurviseJSON) {

            if ($geom instanceof Point) {
                return array($geom->lon, $geom->lat);
            } else {
                return array_map($recurviseJSON, $geom->components);
            }
        };

        return array('type' => static::name, 'coordinates' => call_user_func($recurviseJSON, $this));
    }

    public function toGeoJSON()
    {
        return json_encode((object) $this->toGeoArray());
    }

    public function toKML()
    {
        return '<MultiGeometry>' .
        implode(
            "",
            array_map(
                function ($comp) {
                    return $comp->toKML();
                },
                $this->components
            )
        )
        . '</MultiGeometry>';
    }
}
