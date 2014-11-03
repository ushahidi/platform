<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Filesystem
 *
 * Implemented using Flysystem
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Filesystem;
use League\Flysystem\Filesystem as FlyFs;

class Ushahidi_Filesystem extends FlyFs implements Filesystem
{
	// Class exists only to fufill implementation requirements
}
