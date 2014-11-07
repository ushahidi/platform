<?php

/**
 * Ushahidi Collection Loader Trait
 *
 * Provides a `getCollection(Array $results)` method for repositories.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

use Ushahidi\Core\Entity;

trait CollectionLoader
{
	/**
	 * Get the entity for this repository.
	 * @param  Array  $data
	 * @return Ushahidi\Core\Entity
	 */
	abstract public function getEntity(Array $data = null);

	/**
	 * Converts an array of results into an array of entities,
	 * indexed by the entity id.
	 * @param  Array $results
	 * @return Array
	 */
	protected function getCollection(Array $results)
	{
		$collection = [];
		foreach ($results as $row) {
			$entity = $this->getEntity($row);
			$collection[$entity->getId()] = $entity;
		}
		return $collection;
	}
}
