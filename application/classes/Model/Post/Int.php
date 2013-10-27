<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Post_Int
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
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
		return Arr::merge(parent::rules(), array(
			'value' => array(
				array('numeric')
			)
		));
	}
}