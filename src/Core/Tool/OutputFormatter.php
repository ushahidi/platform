<?php

/**
 * Ushahidi Platform Output Formatter
 *
 * Meant to be used in combination with Formatter interface!
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

interface OutputFormatter
{
	/**
	 * Get the MIME type of a format.
	 * @return  string
	 */
	public function getMimeType();
}
