<?php

/**
 * Ushahidi Platform Formatter Tool
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

interface Formatter
{
	/**
	 * @param  mixed $input
	 * @return mixed
	 * @throws \Ushahidi\Core\Exception\FormatterException
	 */
	public function __invoke($input);
}
