<?php

/**
 * Ushahidi Platform Form Group Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Form;

use Ushahidi\Core\Data;

class FormGroupData extends Data
{
	public $id;
	public $form_id;
	public $label;
	public $priority;
	public $icon;
}
