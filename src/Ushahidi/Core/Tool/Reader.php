<?php

/**
 * Ushahidi Reader
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

use League\Csv\Reader as CSVReader;
use Ushahidi\Contracts\Reader as ReaderInterface;

class Reader extends CSVReader implements ReaderInterface
{
    // intentionally left blank
}
