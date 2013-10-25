<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Post_Point
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Model_Post_Point extends Model_Post_Geometry {

	// Table Name
	protected $_table_name = 'post_point';
	
	/**
	 * Does this attribute type have complex (ie. array) values?
	 * @var boolean 
	 **/
	protected $_complex_value = TRUE;

	/**
	 * Rules for the post_point model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return Arr::merge(parent::rules(), array(
			'value' => array(

			),
		));
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
		// Decode WKT value
		if (array_key_exists($column, $this->_object)
				AND $column == 'value'
				AND is_string($this->_object[$column])
			)
		{
			$decoder = new gisconverter\WKT();
			try
			{
				$geometry = $decoder->geomFromText($this->_object[$column]);
				if ($geometry instanceof gisconverter\Point) return array('lon' => $geometry->lon, 'lat' => $geometry->lat);
			}
			catch (gisconverter\InvalidText $itex) {
				// noop - continue to return raw value
			}

			// continue to return raw value
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
		// Convert to WKT Point
		if ($column == 'value'
				AND is_array($value)
				AND array_key_exists('lat', $value) AND Valid::numeric($value['lat'])
				AND array_key_exists('lon', $value) AND Valid::numeric($value['lon'])
			)
		{
			$value = strtr("POINT(lon lat)", $value);
		}

		return parent::set($column, $value);
	}

}