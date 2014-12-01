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

interface Entity
{
	/**
	 * Return the unique ID for the entity.
	 *
	 * @return Mixed
	 */
	public function getId();

	/**
	 * Return the resource name for this entity.
	 *
	 * @return String
	 */
	public function getResource();

	/**
	 * Get the current entity state as an associative array.
	 *
	 * @return Array
	 */
	public function asArray();

	/**
	 * Change the internal state of the entity, updating values and tracking any
	 * changes that are made.
	 *
	 * @param  Array  $data
	 * @return $this
	 */
	public function setState(Array $data);

	/**
	 * Get all values that have been changed since initial state was defined.
	 *
	 * @return Array
	 */
	public function getChanged();
}
