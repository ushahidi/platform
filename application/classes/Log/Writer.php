<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Log Writer, extended with custom fixes
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Log
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

abstract class Log_Writer extends Kohana_Log_Writer {

	/**
	 * Formats a log entry.
	 *
	 * Fixes PHP 5.4 compatibility issues
	 *
	 * @param   array   $message
	 * @param   string  $format
	 * @return  string
	 */
	public function format_message(array $message, $format = "time site --- level: body in file:line")
	{
		$dbconfig = service('db.config'); // hacky - use db name as fallback
		$message['site'] = service('site') ?: $dbconfig['connection']['database'];

		return parent::format_message(array_filter($message, 'is_scalar'), $format);
	}

}
