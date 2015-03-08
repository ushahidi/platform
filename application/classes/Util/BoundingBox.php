<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Bounding Box class
 *
 * Used for store and convert bounding boxes when
 * filtering posts
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Util
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html
 *             GNU Affero General Public License Version 3 (AGPL3)
 */

use Symm\Gisconverter\Geometry\Point;
use Symm\Gisconverter\Geometry\Polygon;
use Symm\Gisconverter\Geometry\LinearRing;

/**
 * Bounding Box class
 */
class Util_BoundingBox {
	public $north;
	public $south;
	public $east;
	public $west;

	public function __construct($west, $north, $east, $south)
	{
		$this->west = floatval($west);
		$this->north = floatval($north);
		$this->east = floatval($east);
		$this->south = floatval($south);
	}

	/**
	 * Expand each wall of the bounding box outwardby the given number of kilometers.
	 * @param  integer $km [description]
	 * @return [type]      [description]
	 */
	public function expandByKilometers($km = 0)
	{
		if (!$km) { return $this; }

		$origin = (object)[
			'north' => $this->north,
			'east'  => $this->east,
			'south' => $this->south,
			'west'  => $this->west,
		];

		$this->north = $this->newPointByVector(
			$origin->north, $origin->west, $km, 0
		)[0];

		$this->east = $this->newPointByVector(
			$origin->north, $origin->east, $km, 90
		)[1];

		$this->south = $this->newPointByVector(
			$origin->south, $origin->west, $km, 180
		)[0];

		$this->west = $this->newPointByVector(
			$origin->north, $origin->west, $km, 270
		)[1];

		return $this;
	}

	/**
	 * Return a new point given an initial point ($lat, $lon)
	 * a $distance in kilometers, and the angle (in degrees) of the bearing.
	 *
	 * Calculations are based on http://williams.best.vwh.net/avform.htm#LL
	 *
	 * @param  float $lat      latitude of the initial point
	 * @param  float $lon      longitude of the initial point
	 * @param  float $distance distance away in kilometers of the resulting point
	 * @param  float $angle    bearing in degrees, with 0 being North
	 * @return Array           an array in [<lat>, <lon>] format
	 */
	protected function newPointByVector($lat ,$lon, $km, $angle)
	{
		// convert units to radians
		$lat = deg2rad($lat);
		$lon = deg2rad($lon);
		$true_course = deg2rad($angle);

		$earth_radius_km = 6371;
		$d = $km / $earth_radius_km;

		$mod = function($y, $x) {
			return $y - $x * floor($y / $x);
		};

		$new_lat = asin(
			sin($lat) * cos($d) + cos($lat) * sin($d) * cos($true_course)
		);

		if (cos($new_lat) === 0) {
			$new_lon = $lon; // endpoint a pole
		} else  {
			$new_lon = $mod(
				$lon - asin(sin($true_course) * sin($d) / cos($new_lat)) + M_PI,
				2 * M_PI
			) - M_PI;
		}

		return [rad2deg($new_lat), rad2deg($new_lon)];
	 }

	public function toGeometry()
	{
		return new Polygon(array(
				new LinearRing(array(
					new Point(array($this->west, $this->north)),
					new Point(array($this->east, $this->north)),
					new Point(array($this->east, $this->south)),
					new Point(array($this->west, $this->south)),
					new Point(array($this->west, $this->north))
				))
			));
	}

	public function toWKT()
	{
		return strtr("POLYGON((:west :north,:east :north,:east :south,:west :south,:west :north))", array(
			':west' => $this->west,
			':north' => $this->north,
			':east' => $this->east,
			':south' => $this->south
		));
	}

	public function as_array()
	{
		return array($this->west, $this->north, $this->east, $this->south);
	}
}
