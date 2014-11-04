<?php

/**
 * Ushahidi Form
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity;

class Form extends Entity
{
	public $id;
	public $parent_id;
	public $name;
	public $description;
	public $disabled;
	public $created;
	public $updated;

	public function getResource()
	{
		return 'forms';
	}
}
