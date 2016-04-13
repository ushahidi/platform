<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi CSV Formatter
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Formatter;
use Ushahidi\Core\Tool\OutputFormatter;

class Ushahidi_Formatter_Export_CSV implements Formatter, OutputFormatter
{
	// Formatter
	public function __invoke($input)
	{
		// CSV output
	}

	// OutputFormatter
	public function getMimeType()
	{
		return 'application/csv';
	}
}

