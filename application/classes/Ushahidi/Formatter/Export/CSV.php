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

class Ushahidi_Formatter_Export_CSV implements Formatter
{
	// Formatter
	public function __invoke($input)
	{
		// Create filename from deployment name
		$site_name = Kohana::$config->load('site.name');
		$filename = $site_name.'.csv';

		// Send response as CSV download
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename='.$filename);
		header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');

		$fp = fopen('php://output', 'w');
		foreach ($input as $row)
		{
			fputcsv($fp, $row);
		}

		fclose($fp);

		// No need for further processing after download
		exit;
	}
}

