<?php

namespace Symm\Gisconverter\Geometry;
use Symm\Gisconverter\Exceptions\InvalidFeature;

class MultiPoint extends Collection
{
    const name = "MultiPoint";

    public function __construct($components)
    {
        foreach ($components as $comp) {
            if (!($comp instanceof Point)) {
                throw new InvalidFeature(__CLASS__, static::name . " can only contain Point elements");
            }
        }

        $this->components = $components;
    }

    public function equals(Geometry $geom)
    {
        if (get_class($geom) != get_class($this)) {
            return false;
        }

        if (count($this->components) != count($geom->components)) {
            return false;
        }

        foreach (range(0, count($this->components) - 1) as $count) {
            if (!$this->components[$count]->equals($geom->components[$count])) {
                return false;
            }
        }

        return true;
    }

}
