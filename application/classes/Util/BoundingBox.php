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
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use gisconverter\Point,
	gisconverter\Polygon,
	gisconverter\LinearRing ;

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