<?php

/**
 * Ushahidi Platform Tag Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Set;

use Ushahidi\Core\Data;

class SetData extends Data
{
	public $id;
	public $name;
	public $filter;
	public $user_id;
	public $created;
	public $updated;
}
