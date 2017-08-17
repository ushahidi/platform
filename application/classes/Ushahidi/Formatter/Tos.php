<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for CSV
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Ushahidi_Formatter_Tos extends Ushahidi_Formatter_API
{
    use FormatterAuthorizerMetadata;

	protected function format_agreement_date($value)
	{
		return $value ? $value->format(DateTime::W3C) : NULL;
	}

	protected function format_tos_version_date($value)
	{
		return $value ? $value->format(DateTime::W3C) : NULL;
	}

}
