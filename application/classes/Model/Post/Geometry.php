<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Post_Geometry
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Post_Geometry extends ORM {
	/**
	 * A post_geometry belongs to a post and form_attribute
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'post' => array(),
		'form_attribute' => array(),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);

	// Table Name
	protected $_table_name = 'post_geometry';

	/**
	 * Rules for the post_int model
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
	 * Updates or Creates the record depending on loaded()
	 * Overriding this method to handle WKT Geometry value
	 *
	 * @chainable
	 * @param  Validation $validation Validation object
	 * @return ORM
	 */
	public function save(Validation $validation = NULL)
	{
		$original_value = FALSE;
		if (is_string($this->_object['value']))
		{
			$original_value = $this->_object['value'];
			$this->_object['value'] = DB::expr('GeomFromText(:wkt)', array(':wkt' => $this->_object['value']));
		}

		parent::save($validation);

		if ($original_value)
		{
			$this->_object['value'] = $original_value;
		}
	}

	/**
	 * Returns an array of columns to include in the select query.
	 * Overridding to add custom handling for Geometry value
	 *
	 * @return array Columns to select
	 */
	protected function _build_select()
	{
		$columns = array();

		foreach ($this->_table_columns as $column => $_)
		{
			if ($column == 'value')
			{
				$columns[] = array(
					DB::expr( 'AsText('. $this->_db->quote_column($this->_object_name.'.'.$column) .')' ),
					$column
				);
			}
			else
			{
				$columns[] = array($this->_object_name.'.'.$column, $column);
			}
		}

		return $columns;
	}
}