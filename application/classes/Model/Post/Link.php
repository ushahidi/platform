<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Post_Link
 * 
 * This model extends Post_Varchar and uses the same table
 * but validates values as urls
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
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