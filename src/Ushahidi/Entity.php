<?php

/**
 * Ushahidi Platform Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi;

use Ushahidi\Traits\ArrayExchange;

abstract class Entity
{
	use ArrayExchange;

	/**
	 * Return the resource name for authorization.
	 * @return string
	 */
	abstract public function getResource();
}
