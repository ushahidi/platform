<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Post_Datetime
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Post_Datetime extends Model_Post_Value {

	// Table Name
	protected $_table_name = 'post_datetime';

	/**
	 * Filters for the post_datetime model
	 * 
	 * @return array Filters
	 */
	public function filters()
	{
		return array(
			'value' => array(
				// Filter to handle special value 'unknown'
				array(function($value) { return $value == 'unknown' ? FALSE : $value; }, array(':value')),
				// @todo handle 'now' ?
			),
		);
	}

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
}