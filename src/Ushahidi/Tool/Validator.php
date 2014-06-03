<?php

/**
 * Ushahidi Platform Validator Tool
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tool;

use Ushahidi\Data;

interface Validator
{
	/**
	 * @param  Ushahidi\Data to be checked
	 * @return bool
	 */
	public function check(Data $entity);

	/**
	 * @param  String  $source
	 * @return Array
	 */
	public function errors($source = null);
}
