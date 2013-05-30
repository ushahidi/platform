<?php defined('SYSPATH') or die('No direct script access.');

/**
 * ORM parent class - with extra Ushahidi extensions
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class ORM extends Kohana_ORM {
	
	/**
	 * Callback function to check if fk exists
	 */
	public function fk_exists($model_name, $field, $value)
	{
		return ORM::factory($model_name, $value)->loaded();
	}
	
}
