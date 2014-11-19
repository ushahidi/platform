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

		if (isset($data['password']))
		{
			unset($data['password']);
		}

		return $data;
	}
}
