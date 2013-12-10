<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Post_Datetime
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Model_Post_Datetime extends Model_Post_Value {

	// Table Name
	protected $_table_name = 'post_datetime';

	/**
	 * Rules for the post_datetime model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return Arr::merge(parent::rules(), array(
			'value' => array(
				array('date')
			)
		));
	}

	/**
	 * Handles getting of column
	 * Overriding this method to handle JS style dates
	 *
	 * @param   string $column Column name
	 * @throws Kohana_Exception
	 * @return mixed
	 */
	public function get($column)
	{
		if (array_key_exists($column, $this->_object)
				AND $column == 'value'
			)
		{
			return ($date = DateTime::createFromFormat('Y-m-d H:i:s', $this->_object[$column]))
				? $date->format(DateTime::W3C)
				: $this->_object[$column];
		}

		return parent::get($column);
	}


	/**
	 * Handles setting of columns
	 * Overriding this method to handle JS style dates
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
			// Try to convert W3C format first
			$date = DateTime::createFromFormat(DateTime::W3C, $value);
			// If that failed, try standard strtotime
			if (! $date)
			{
				$date = date_create($value);
			}

			// Output date in MySQL format
			if ($date)
			{
				$value = $date->format('Y-m-d H:i:s');
			}
		}

		return parent::set($column, $value);
	}
}