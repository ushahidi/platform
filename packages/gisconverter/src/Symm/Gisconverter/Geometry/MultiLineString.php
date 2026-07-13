<?php

namespace Symm\Gisconverter\Geometry;

class MultiLineString extends Collection
{
    const name = "MultiLineString";

    public function __construct($components)
    {
        foreach ($components as $comp) {
            if (!($comp instanceof LineString)) {
                throw new InvalidFeature(__CLASS__, "MultiLineString can only contain LineString elements");
            }
        }

        $this->components = $components;
    }
}
