<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Password Hasher
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Hasher;

class Ushahidi_Hasher_Password implements Hasher
{
	public function hash($password)
	{
		return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
	}
}
