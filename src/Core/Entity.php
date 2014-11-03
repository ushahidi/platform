<?php

/**
 * Ushahidi Platform Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core;

use Ushahidi\Core\Traits\ArrayExchange;

abstract class Entity
{
	use ArrayExchange;

	/**
	 * Return the Unique ID for the entity
	 * @var Mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Return the resource name for authorization.
	 * @return string
	 */
	abstract public function getResource();
}
