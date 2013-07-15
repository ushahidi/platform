<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Bounding Box class
 * 
 * Used for store and convert bounding boxes when
 * filtering posts
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Util
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
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
					new Point(array($bb_west, $bb_north)),
					new Point(array($bb_east, $bb_north)),
					new Point(array($bb_east, $bb_south)),
					new Point(array($bb_west, $bb_south)),
					new Point(array($bb_west, $bb_north))
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
}