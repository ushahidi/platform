<?php

/**
 * Ushahidi Reader Factory
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tools\FileReader;

use Ushahidi\Core\Tools\Reader;
use Ushahidi\Contracts\ReaderFactory;

class CSVReaderFactory implements ReaderFactory
{
    public function createReader($file)
    {
        return $file instanceof \SplFileObject
            ? Reader::createFromFileObject($file)
            : Reader::createFromPath($file);
    }
}
