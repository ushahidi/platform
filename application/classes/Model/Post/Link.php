<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Post_Link
 * 
 * This model extends Post_Varchar and uses the same table
 * but validates values as urls
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Model_Post_Link extends Model_Post_Varchar {

	/**
	 * Rules for the post_varchar model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return Arr::merge(parent::rules(), array(
			'value' => array(
				array('url')
			)
		));
	}
}