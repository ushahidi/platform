<?php

/**
 * Ushahidi Platform Form Attribute Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Form;

use Ushahidi\Core\Data;

class FormAttributeData extends Data
{
	public $id;
	public $key;
	public $label;
	public $input;
	public $type;
	public $required;
	public $default;
	public $priority;
	public $options;
	public $cardinality;
	public $form_group_id;
	public $form_id;
}
