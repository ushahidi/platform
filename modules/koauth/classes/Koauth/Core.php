<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Koauth helper class
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Koauth
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Koauth_Core {
	
	public static function auto_load($class)
	{
		return Kohana::auto_load($class, 'vendor/oauth2-server-php/src');
	}
	
}