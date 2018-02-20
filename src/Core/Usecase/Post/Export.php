<?php

/**
 * Ushahidi Platform Entity Search Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Usecase\SearchUsecase;

class Export extends SearchUsecase
{
	// - VerifyParentLoaded for checking that the parent exists
	use VerifyParentLoaded;

	/**
	 * Get filter parameters that are used for paging.
	 *
	 * @return Array
	 */
	protected function getPagingFields()
	{
		return [
			'orderby' => 'created',
			'order'   => 'asc',
			'limit'   => null,
			'offset'  => 0
		];
	}

	// Usecase
	public function interact()
	{
		// Fetch an empty entity...
		$entity = $this->getEntity();

		// ... verify that the entity can be searched by the current user
		$this->verifySearchAuth($entity);

		// ... and get the search filters for this entity
		$search = $this->getSearch();

		// ... pass the search information to the repo
		$this->repo->setSearchParams($search);

		// ... get the results of the search
		$results = $this->repo->getSearchResults(true);

		// ... get the total count for the search
		$total = $this->repo->getSearchTotal();

		// ... remove any entities that cannot be seen
		$priv = 'read';
		foreach ($results as $idx => $entity) {
			if (!$this->auth->isAllowed($entity, $priv)) {
				unset($results[$idx]);
			}

			// Retrieved Attribute Labels for Entity's values
			$data = $entity->asArray();
			$data = $this->repo->retrieveColumnNameData($data);

			$results[$idx] = $data;
		}

		// ... pass the search information to the formatter, for paging
		// TODO: Refactor: This line appears to be totally unused within the
		// actual formatter
		$this->formatter->setSearch($search, $total);

		// ... and return the formatted results.
		return $this->formatter->__invoke($results);
	}
}
