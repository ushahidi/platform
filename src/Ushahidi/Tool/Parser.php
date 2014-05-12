<?php

/**
 * Ushahidi Platform Parser Tool
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tool;

interface Parser
{
	/**
	 * @param  array data to parse
	 * @return object parsed entity
	 * @throws Ushahidi\Exception\Parser
	 */
	public function __invoke(Array $data);
}
