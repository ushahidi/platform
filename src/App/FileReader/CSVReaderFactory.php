<?php

/**
 * Ushahidi Reader Factory
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\FileReader;

use Ushahidi\Core\Tool\ReaderFactory;
use Ushahidi\App\Reader;

class CSVReaderFactory implements ReaderFactory
{
	public function createReader($file)
	{
		return $file instanceof \SplFileObject
			? Reader::createFromFileObject($file)
			: Reader::createFromPath($file);
	}
}
