<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi JSON Formatter
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Tool\Formatter;
use Ushahidi\Tool\OutputFormatter;
use Ushahidi\Exception\Formatter as FormatterException;

class Ushahidi_Formatter_JSON implements Formatter, OutputFormatter
{
	// Formatter
	public function __invoke($input)
	{
		// Are we in development environment?
		$dev_env = Kohana::$environment === Kohana::DEVELOPMENT;
		$options = $dev_env ? JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES : null;

		$json = json_encode($input, $options);

		if ($json === FALSE)
			throw new FormatterException('Unable to format data as JSON: ' . json_last_error());

		return $json;
	}

	// OutputFormatter
	public function getMimeType()
	{
		return 'application/json';
	}
}

