<?php

namespace Symm\Gisconverter\Geometry;

use Symm\Gisconverter\Exceptions\InvalidFeature;
use Symm\Gisconverter\Exceptions\Unimplemented;

class LinearRing extends LineString
{
    const name = "LinearRing";

    public function __construct($components)
    {
        $first = $components[0];
        $last = end($components);

        if (!$first->equals($last)) {
            throw new InvalidFeature(__CLASS__, "LinearRing must be closed");
        }

        parent::__construct($components);
    }
    public function contains(Geometry $geom)
    {
        if ($geom instanceof Collection) {
            foreach ($geom->components as $point) {
                if (!$this->contains($point)) {
                    return false;
                }
            }

            return true;
        } elseif ($geom instanceof Point) {
            return $this->containsPoint($geom);
        } else {
            throw new Unimplemented(get_class($this) . "::" . __FUNCTION__ . " for " . get_class($geom) . " geometry");
        }
    }

    protected function containsPoint(Point $point)
    {
        /*
         *PHP implementation of OpenLayers.Geometry.LinearRing.ContainsPoint algorithm
         */
        $px = round($point->lon, 14);
        $py = round($point->lat, 14);

        $crosses = 0;
        foreach (range(0, count($this->components) - 2) as $i) {
            $start = $this->components[$i];
            $x1 = round($start->lon, 14);
            $y1 = round($start->lat, 14);
            $end = $this->components[$i + 1];
            $x2 = round($end->lon, 14);
            $y2 = round($end->lat, 14);

            if ($y1 == $y2) {
                // horizontal edge
                if ($py == $y1) {
                    // point on horizontal line
                    if ($x1 <= $x2 && ($px >= $x1 && $px <= $x2) || // right or vert
                        $x1 >= $x2 && ($px <= $x1 && $px >= $x2)) { // left or vert
                        // point on edge
                        $crosses = -1;
                        break;
                    }
                }
                // ignore other horizontal edges
                continue;
            }

            $cx = round(((($x1 - $x2) * $py) + (($x2 * $y1) - ($x1 * $y2))) / ($y1 - $y2), 14);

            if ($cx == $px) {
                // point on line
                if ($y1 < $y2 && ($py >= $y1 && $py <= $y2) || // upward
                    $y1 > $y2 && ($py <= $y1 && $py >= $y2)) { // downward
                    // point on edge
                    $crosses = -1;
                    break;
                }
            }
            if ($cx <= $px) {
                // no crossing to the right
                continue;
            }
            if ($x1 != $x2 && ($cx < min($x1, $x2) || $cx > max($x1, $x2))) {
                // no crossing
                continue;
            }
            if ($y1 < $y2 && ($py >= $y1 && $py < $y2) || // upward
                $y1 > $y2 && ($py < $y1 && $py >= $y2)) { // downward
                $crosses++;
            }
        }
        $contained = ($crosses == -1) ?
            // on edge
            1 :
            // even (out) or odd (in)
            !!($crosses & 1);

        return $contained;
    }
}
