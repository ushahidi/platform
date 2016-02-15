<?php

/**
 * Ushahidi Platform File Reader Tool
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

interface FileReader
{
	/**
	 * Read a file and return a Traversable object
	 *
	 * @param  SplFileObject|string $file
	 * @return Traversable
	 */
	public function process($file);

	public function setOffset($offset);

	public function setLimit($limit);
}
