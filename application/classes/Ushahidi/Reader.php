<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Reader
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use League\Csv\Reader as CSVReader;
use Ushahidi\Core\Tool\Reader;

class Ushahidi_Reader extends CSVReader implements Reader
{
	// intentionally left blank
}
