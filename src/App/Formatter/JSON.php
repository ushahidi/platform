<?php

/**
 * Ushahidi JSON Formatter
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Formatter;

use Kohana;
use Ushahidi\Core\Tool\Formatter;
use Ushahidi\Core\Tool\OutputFormatter;
use Ushahidi\Core\Exception\FormatterException;

class JSON implements Formatter, OutputFormatter
{
	protected function getOptions()
	{
		// Are we in development environment?
		return env('APP_DEBUG', false) ? JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES : null;
	}

	// Formatter
	public function __invoke($input)
	{
		$opts = $this->getOptions();
		$json = json_encode($input, $opts);

		if ($json === false) {
			throw new FormatterException('Unable to format data as JSON: ' . json_last_error());
        }

		return $json;
	}

	// OutputFormatter
	public function getMimeType()
	{
		return 'application/json';
	}
}
