<?php defined('SYSPATH') OR die('No direct access allowed.');

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

class Util_Tile {

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

		return new Util_BoundingBox($bb_west, $bb_north, $bb_east, $bb_south);
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
