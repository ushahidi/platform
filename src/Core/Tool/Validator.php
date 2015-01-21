<?php

/**
 * Ushahidi Platform Validator Tool
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

use Ushahidi\Core\Entity;

interface Validator
{
	// Regex that only allows letters, numbers, punctuation, and space.
	const REGEX_STANDARD_TEXT = '/^[\pL\pN\pP ]++$/uD';

	/**
	 * @param  Entity to be checked
	 * @return bool
	 */
	public function check(Entity $entity);

	/**
	 * @param  String  $source
	 * @return Array
	 */
	public function errors($source = null);
}
