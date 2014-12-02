<?php

/**
 * Ushahidi Platform Data Import User Writer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\DataImport\Writer;

use Ushahidi\Core\Usecase\User\UserData;

class UserWriter extends RepositoryWriter
{

	/**
	 * Create a Data object from item
	 * @param  Array $item
	 * @return Data
	 */
	protected function createDataObject(array $item)
	{
		return new UserData($item);
	}
}
