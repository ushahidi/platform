<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Post_Int
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Post_Int extends Model_Post_Value {

	// Table Name
	protected $_table_name = 'post_int';

	/**
	 * Rules for the post_int model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'value' => array(
				array('numeric')
			)
		);
	}
}