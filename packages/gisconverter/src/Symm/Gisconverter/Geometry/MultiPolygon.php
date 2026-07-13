<?php
/**
 * Created by PhpStorm.
 * User: gaz
 * Date: 29/11/2013
 * Time: 14:53
 */

namespace Symm\Gisconverter\Geometry;

class MultiPolygon extends Collection
{
    const name = "MultiPolygon";

    public function __construct($components)
    {
        foreach ($components as $comp) {
            if (!($comp instanceof Polygon)) {
                throw new InvalidFeature(__CLASS__, "MultiPolygon can only contain Polygon elements");
            }
        }

        $this->components = $components;
    }
}
