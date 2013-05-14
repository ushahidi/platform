<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Post_Point
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Post_Point extends Model_Post_Geometry {

	// Table Name
	protected $_table_name = 'post_point';

	/**
	 * Rules for the post_point model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'value' => array(

			)
		);
	}

	/**
	 * Handles getting of column
	 * Overriding this method to handle WKT Geometry value
	 *
	 * @param   string $column Column name
	 * @throws Kohana_Exception
	 * @return mixed
	 */
	public function get($column)
	{
		// Decode WKB value
		if (array_key_exists($column, $this->_object) AND $column == 'value')
		{
			$point = geoPHP::load($this->_object[$column], 'wkt');
			if ($point instanceof Geometry) return array('lon' => $point->x(), 'lat' => $point->y());
		}

		return parent::get($column);
	}


	/**
	 * Handles setting of columns
	 * Overriding this method to handle WKT Geometry value
	 *
	 * @param  string $column Column name
	 * @param  mixed  $value  Column value
	 * @throws Kohana_Exception
	 * @return ORM
	 */
	public function set($column, $value)
	{
		if ($column == 'value')
		{
			$point = new Point($value['lon'], $value['lat']);
			$value = $point->out('wkt');
		}

		return parent::set($column, $value);
	}

}