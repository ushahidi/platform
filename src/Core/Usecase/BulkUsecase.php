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
use Ushahidi\Core\Tool\ValidatorTrait;
use Ushahidi\Core\Traits\FilterRecords;
use Ushahidi\Core\Traits\IdentifyRecords;
use Ushahidi\Core\Traits\ModifyRecords;

class BulkUsecase implements Usecase
{
	// Uses several traits to assign tools. Each of these traits provides a
	// setter method for the tool. For example, the AuthorizerTrait provides
	// a `setAuthorizer` method which only accepts `Authorizer` instances.
	use AuthorizerTrait,
		FormatterTrait,
		ValidatorTrait;

	// - FilterRecords for setting search parameters
	use FilterRecords;

	// - IdentifyRecords for setting entity lookup parameters
	// - ModifyRecords for setting entity modification parameters
	use IdentifyRecords,
		ModifyRecords;

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
		return true;
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
		
		$this->verifyValid($entity);

		// ... and get the search filters for this entity
		$search = $this->getSearch();

		// ... pass the search information to the repo
		$this->repo->setSearchParams($search);

		// ... get the results of the search
		$results = $this->repo->getSearchResults();
		
		$records = array_keys($results);
		
		$this->validateRecords($records);

		if (sizeof($records) > 0)
		{
			// ... execute action against the $results
			$total = $this->executeActions($records);
		}
		else
		{
			$total = 0;
		}
		
		$actions = $this->getActions();
		
		// ... and return the formatted results.
		return $this->formatter->__invoke([$total, $actions]);
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
	 * Get filter parameters and default values that are used for paging.
	 *
	 * @return Array
	 */
	protected function getPagingFields()
	{
		return [
			'orderby' => 'id',
			'order'   => 'asc',
			'limit'   => null,
			'offset'  => 0
		];
	}

	/**
	 * Get filter parameters as search data.
	 *
	 * @return SearchData
	 */
	protected function getSearch()
	{
		$fields = array_flip($this->repo->getSearchFields());
		$paging = $this->getPagingFields();
		
		$filters = array_intersect_key($this->getPayload('filters'), $fields);
		
		if (!isset($filters['status']))
		{		
			$actions = $this->getActions();
			
			$status_arr = ['published','draft','archived'];
			
			$statuses = isset($actions['status']) ? array_diff($status_arr, [$actions['status']]) : $status_arr;
	
			$filters += ['status' => $statuses];
		}
		
		$this->search->setFilters($filters);

		return $this->search;
	}

	// ValidatorTrait
	protected function verifyValid($entity)
	{
		if (!$this->validator->check($this->payload)) {
			$this->validatorError($entity);
		}
	}

	/**
	 * Execute execute validations against the result set
	 *
	 * @return null
	 */
	protected function validateRecords($records)
	{
		return;	
	}

	/**
	 * Execute actions against the result set
	 *
	 * @return null
	 */
	protected function executeActions($results)
	{
		return;	
	}

	/**
	 * Get actions from the payload
	 *
	 * @return null
	 */
	protected function getActions()
	{
		return $this->getPayload('actions');
	}
}
