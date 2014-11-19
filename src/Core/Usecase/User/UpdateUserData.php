<?php

/**
 * Ushahidi Platform User Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\User;

use Ushahidi\Core\Data;

class UpdateUserData extends UserData
{
	public function getDifferent(Array $compare)
	{
		// There probably is a smarter way to keep id of the updatee then this
		// needed for validation class - pay attention when reviewing
		$delta = array_diff_assoc($this->asArray(), $compare);
		$delta['id'] = $this->asArray()['id'];

		return new static(array_filter($delta)); // filter out empty values
	}
}
