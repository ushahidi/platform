<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Post Values
 *
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Formatter;

class Ushahidi_Formatter_Post_Datetime implements Formatter
{
	public function __invoke($data)
	{
		if ($data['value']) {
			return date(DateTime::W3C, strtotime($data['value']));
		}
		return null;
	}
}
