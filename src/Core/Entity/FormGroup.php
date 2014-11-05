<?php

/**
 * Ushahidi Form Group
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity;

class FormGroup extends Entity
{
	public $id;
	public $form_id;
	public $label;
	public $priority;
	public $icon;

	public function getResource()
	{
		return 'form_groups';
	}
}
