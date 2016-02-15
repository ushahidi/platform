<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Reader Factory
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\ReaderFactory;

class Ushahidi_CSVReaderFactory implements ReaderFactory
{
	public function createReader($file)
	{
		return $file instanceof \SplFileObject
			? Ushahidi_Reader::createFromFileObject($file)
			: Ushahidi_Reader::createFromPath($file);
	}
}
