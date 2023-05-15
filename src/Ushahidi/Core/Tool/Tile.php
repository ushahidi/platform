<?php

/**
 * Tile utility class
 *
 * Handles converting slippy map tile numbers to lat/lon values
 * Ported from:
 * http://svn.openstreetmap.org/applications/routing/pyroute/tilenames.py
 * http://wiki.openstreetmap.org/wiki/Slippy_map_tilenames#Java
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Util
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html
 *             GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

class Tile
{

  
    /**
     * Get the bounding box for a set of tile values
     *
     * @todo better handling on tiles that are out of range
     * @param int $zoom
     * @param int $x
     * @param int $y
     * @return BoundingBox
     */
    // public static function tileToBoundingBox($zoom, $x, $y)
    // {

    //     return $this->getPointBbox();
    // }

    public static function tileToBoundingBox($zoom, $lon, $lat)
    {
        $n = pow(2, $zoom);
        $x = intval(($lon + 180) / 360 * $n);
        $y = intval((1 - log(tan(deg2rad($lat)) + 1 / cos(deg2rad($lat))) / pi()) / 2 * $n);
        $tile_bbox = self::getTileBbox($x, $y, $zoom);
        return $tile_bbox;
    }
    
    private static function getTileBbox($x, $y, $zoom)
    {
        $n = pow(2, $zoom);
        $lon_left = $x / $n * 360.0 - 180.0;
        $lat_top = atan(sinh(pi() * (1 - 2 * $y / $n))) * 180.0 / pi();
        $lon_right = ($x + 1) / $n * 360.0 - 180.0;
        $lat_bottom = atan(sinh(pi() * (1 - 2 * ($y + 1) / $n))) * 180.0 / pi();
        return new BoundingBox($lon_left, $lat_bottom, $lon_right, $lat_top);
    }
}
