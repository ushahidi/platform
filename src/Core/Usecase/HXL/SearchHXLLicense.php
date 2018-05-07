<?php
/**
 * Created by PhpStorm.
 * User: rowasc
 * Date: 5/7/18
 * Time: 2:37 PM
 */

namespace Ushahidi\Core\Usecase\HXL;

use Ushahidi\Core\Usecase\SearchUsecase;

class SearchHXLLicense extends SearchUsecase
{

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

		// ... pass the search information to the formatter, for paging
		$this->formatter->setSearch($search, $total);

		// ... and return the formatted results.
		return $this->formatter->__invoke($results);
	}
}
