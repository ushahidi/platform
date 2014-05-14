<?php

/**
 * Ushahidi Repository for Collections use case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\API;

use Ushahidi\Entity;

interface CollectionRepository
{
	/**
	 * @param  \Ushahidi\Entity $entity
	 * @param  Array            $search
	 * @param  Array            $order
	 * @param  Integer          $limit
	 * @param  Integer          $offset
	 * @return Array [\Ushahidi\Entity, ...]
	 */
	public function search(
		Entity $entity,
		array  $search,
		array  $order = null,
		       $limit = null,
		       $offset = null);

	/**
	 * @param  \Ushahidi\Entity
	 * @return Boolean
	 */
	public function create(Entity $entity);

	/**
	 * @param  \Ushahidi\Entity
	 * @return Array [\Ushahidi\Entity, ...]
	 */
	public function read(Entity $entity);

	/**
	 * @param  \Ushahidi\Entity
	 * @return Boolean
	 */
	public function update(Entity $entity);

	/**
	 * @param  \Ushahidi\Entity
	 * @return Boolean
	 */
	public function delete(Entity $entity);
}
