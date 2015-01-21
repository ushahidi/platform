<?php

/**
 * Ushahidi Platform Search Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase;

use Ushahidi\Core\Usecase;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Traits\FilterRecords;

class SearchUsecase implements Usecase
{
	// Uses several traits to assign tools. Each of these traits provides a
	// setter method for the tool. For example, the AuthorizerTrait provides
	// a `setAuthorizer` method which only accepts `Authorizer` instances.
	use AuthorizerTrait,
		FormatterTrait;

	// - FilterRecords for setting search parameters
	use FilterRecords;

	/**
	 * @var SearchData
	 */
	protected $search;

	/**
	 * @param SearchData $search
	 */
	public function setData(SearchData $search)
	{
		$this->search = $search;
	}

	/**
	 * @var SearchRepository
	 */
	protected $repo;

	/**
	 * Inject a repository that can search for entities.
	 *
	 * @param  SearchRepository $repo
	 * @return $this
	 */
	public function setRepository(SearchRepository $repo)
	{
		$this->repo = $repo;
		return $this;
	}

	// Usecase
	public function isWrite()
	{
		return false;
	}

	// Usecase
	public function isSearch()
	{
		return true;
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
		$results = $this->repo->getSearchResults();

		// ... get the total count for the search
		$total = $this->repo->getSearchTotal();

		// ... remove any entities that cannot be seen
		$priv = 'read';
		foreach ($results as $idx => $entity) {
			if (!$this->auth->isAllowed($entity, $priv)) {
				unset($results[$idx]);
			}
		}

		// ... pass the search information to the formatter, for paging
		$this->formatter->setSearch($search, $total);

		// ... and return the formatted results.
		return $this->formatter->__invoke($results);
	}

	/**
	 * Get an empty entity.
	 *
	 * @return Entity
	 */
	protected function getEntity()
	{
		return $this->repo->getEntity();
	}

	/**
	 * Get filter parameters that are used for paging.
	 *
	 * @return Array
	 */
	protected function getPagingFields()
	{
		return ['orderby', 'order', 'limit', 'offset'];
	}

	/**
	 * Get filter parameters as search data.
	 *
	 * @return SearchData
	 */
	protected function getSearch()
	{
		$fields = $this->repo->getSearchFields();
		$paging = $this->getPagingFields();

		$filters = $this->getFilters(array_merge($fields, $paging));

		$this->search->setFilters($filters);
		$this->search->setSorting($paging);

		return $this->search;
	}

	public function getSearchTotal()
	{
		return $this->repo->getSearchTotal();
	}
}
