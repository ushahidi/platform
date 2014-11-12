<?php

/**
 * Ushahidi Platform Admin Update Message Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Message;

interface UpdateMessageRepository
{
	/**
	 * @param  String $status
	 * @param  String $direction
	 * @return Boolean
	 */
	public function checkStatus($status, $direction);
}
