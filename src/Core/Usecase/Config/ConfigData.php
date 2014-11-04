<?php

/**
 * Ushahidi Platform Config Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Config;

use Ushahidi\Core\RawData;

class ConfigData extends RawData
{
	public function asArray()
	{
		$data = parent::asArray();

		unset($data['id']); // id is the group name, which cannot change
	
		return $data;
	}
}
