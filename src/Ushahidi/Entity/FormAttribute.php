<?php

/**
 * Ushahidi Form Attribute
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

use Ushahidi\Entity;

class FormAttribute extends Entity
{
	public $id;
	public $key;
	public $label;
	public $input;
	public $type;
	public $required;
	public $default;
	// @todo move this. priority is really on a property of an attribute *in* a group
	public $priority;
	public $options = [];
	public $cardinality;

	public function getResource()
	{
		return 'form_attributes';
	}
}
