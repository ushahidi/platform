<?php

/**
 * Ushahidi Reader factory interface
 *
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

interface ReaderFactory
{
	/**
	 * @param SplFileObject|string $file
	 */
	public function createReader($file);
}
