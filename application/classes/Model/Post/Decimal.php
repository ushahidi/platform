<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Post_Decimal
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Model_Post_Decimal extends Model_Post_Value {

	// Table Name
	protected $_table_name = 'post_decimal';

	/**
	 * Rules for the post_decimal model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return Arr::merge(parent::rules(), array(
			'value' => array(
				array('not_empty'),
				array('decimal')
			)
		));
	}
}