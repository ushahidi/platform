<?php

namespace Symm\Gisconverter\Interfaces;
use Symm\Gisconverter\Geometry\Geometry;

interface GeometryInterface
{
    /**
     * @return array
     */
    public function toGeoArray();

    /**
     * @return string
     */
    public function toGeoJSON();

    /**
     * @return string
     */
    public function toKML();

    /**
     * @return string
     */
    public function toWKT();

    /**
     * @param mode: trkseg, rte or wpt
     * @return string
     */
    public function toGPX($mode = null);

    /**
     * @param  Geometry $geom
     * @return boolean
     */
    public function equals(Geometry $geom);
}
