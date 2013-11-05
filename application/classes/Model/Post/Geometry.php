<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Post_Geometry
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Model_Post_Geometry extends Model_Post_Value {
	/**
	 * A post_geometry belongs to a post and form_attribute
	 *
	 * @var array Relationships
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
		return Arr::merge(parent::rules(), array(
			'value' => array(
				array(array($this, 'validate_wkt'), array(':value'))
			)
		));
	}

	public function validate_wkt($value)
	{
		if (empty($value)) return TRUE;

		if (! is_string($value)) return FALSE;

		$decoder = new gisconverter\WKT();
		try
		{
			$decoder->geomFromText($value);
		}
		catch (gisconverter\InvalidText $itex) {
			return FALSE;
		}

		return TRUE;
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
		// Validate before replacing value with Database_Expression
		if ( ! $this->_valid OR $validation)
		{
			$this->check($validation);
		}

		$original_value = FALSE;
		if (is_string($this->_object['value']) AND ! empty($this->_object['value']))
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