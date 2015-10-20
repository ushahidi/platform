<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Notifications
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Ushahidi_Formatter_Formattribute extends Ushahidi_Formatter_API
{
	use FormatterAuthorizerMetadata;

	protected function get_field_name($field)
	{
		$remap = [
			'form_stage_id'  => 'form_stage'
			];

		if (isset($remap[$field])) {
			return $remap[$field];
		}

		return parent::get_field_name($field);
	}

	protected function format_form_stage_id($form_stage_id)
	{
		return $this->get_relation('form_stages', $form_stage_id);
	}
}
