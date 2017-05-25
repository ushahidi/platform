<?php

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

namespace Ushahidi\App;

use Ushahidi\Core\Tool\Filesystem as FilesystemInterface;
use League\Flysystem\Filesystem as FlyFs;

class Filesystem extends FlyFs implements FilesystemInterface
{
	// Class exists only to fufill implementation requirements
}
