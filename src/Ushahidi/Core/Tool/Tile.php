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
     * Get number of tiles for whole map
     *
     * @param int $zoom
     * @return int number of tiles
     */
    public static function numTiles($zoom)
    {
        return pow(2, $zoom);
    }

    /**
     * Get the bounding box for a set of tile values
     *
     * @todo better handling on tiles that are out of range
     * @param int $zoom
     * @param int $x
     * @param int $y
     * @return BoundingBox
     */
    public static function tileToBoundingBox($zoom, $x, $y)
    {

        $bb_north = self::tileToLat($y, $zoom);
        $bb_south = self::tileToLat($y + 1, $zoom);
        $bb_west = self::tileToLon($x, $zoom);
        $bb_east = self::tileToLon($x + 1, $zoom);

        return new BoundingBox($bb_west, $bb_north, $bb_east, $bb_south);
    }

    public static function pointToBoundingBox($zoom, $lon, $lat)
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
        $lon_left = round($x / $n * 360.0 - 180.0, 4);
        $lat_top = round(atan(sinh(pi() * (1 - 2 * $y / $n))) * 180.0 / pi(), 4);
        $lon_right = round(($x + 1) / $n * 360.0 - 180.0, 4);
        $lat_bottom = round(atan(sinh(pi() * (1 - 2 * ($y + 1) / $n))) * 180.0 / pi(), 4);
        return new BoundingBox($lon_left, $lat_bottom, $lon_right, $lat_top);
    }

    /**
     * Get longitude from tile x value
     *
     * @param int $x
     * @param int $zoom
     * @return float longitude
     */
    public static function tileToLon($x, $zoom)
    {
        return $x / self::numTiles($zoom) * 360.0 - 180.0;
    }

    /**
     * Get latitude from tile x value
     *
     * @param int $y
     * @param int $zoom
     * @return float latitude
     */
    public static function tileToLat($y, $zoom)
    {
        $n = pi() * (1 - 2 * $y / self::numTiles($zoom));
        return rad2deg(atan(sinh($n)));
    }
}
