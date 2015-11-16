<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Tag
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Ushahidi_Formatter_User extends Ushahidi_Formatter_API
{
	use FormatterAuthorizerMetadata;

	public function __invoke($user)
	{ // prefer doing it here untill we implement parent method for filtering results - mixing and matching with metadata is just plain ugly
		$data = parent::__invoke($user);

		// Generate hash for gravatar
		$data['gravatar'] = !empty($data['email']) ? md5(strtolower(trim($data['email']))) : '00000000000000000000000000000000';

		// Remove password
		if (isset($data['password']))
		{
			unset($data['password']);
		}

		if (!in_array('read_full', $data['allowed_privileges']))
		{
			// Remove sensitive fields
			$data = array_intersect_key($data, array_fill_keys(['id', 'url', 'username', 'realname', 'allowed_privileges'], TRUE));
		}

		return $data;
	}
}
