<?php

/**
 * Ushahidi Platform Create Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase;

use Ushahidi\Core\Entity;

interface CreateRepository
{
	/**
	 * Creates a new record and returns the created id.
	 * @param  Entity $entity
	 * @return Mixed
	 */
	public function create(Entity $entity);

	/**
	 * Fetches a record and returns the entity.
	 * @return Entity
	 */
	public function get($id);

	/**
	 * Converts an array of entity data into an object.
	 * @param  Array $data
	 * @return Entity
	 */
	public function getEntity(Array $data = null);
}
