<?php

/**
 * Ushahidi Platform Search Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase;

use Ushahidi\Data;
use Ushahidi\Usecase;
use Ushahidi\SearchData;
use Ushahidi\Tool\AuthorizerTrait;
use Ushahidi\Traits\VerifySearchData;
use Ushahidi\Exception\AuthorizerException;

class SearchUsecase implements Usecase
{
	// Uses several traits to assign tools. Each of these traits provides a
	// setter method for the tool. For example, the AuthorizerTrait provides
	// a `setAuthorizer` method which only accepts `Authorizer` instances.
	use AuthorizerTrait;

	// - VerifySearchData for additional type hinting
	use VerifySearchData;

	// Ushahidi\Usecase\SearchRepository
	protected $repo;

	public function __construct(Array $tools)
	{
		$this->setAuthorizer($tools['auth']);
		$this->setRepository($tools['repo']);
	}

	private function setRepository(SearchRepository $repo)
	{
		$this->repo = $repo;
	}

	public function interact(Data $search)
	{
		$this->verifySearchData($search);

		$this->repo->setSearchParams($search);
		$results = $this->repo->getSearchResults();

		foreach ($results as $idx => $entity) {
			if (!$this->auth->isAllowed($entity, 'read')) {
				unset($results[$idx]);
			}
		}

		return $results;
	}
}
