<?php

/**
 * Ushahidi Platform Search Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Config;

use Ushahidi\Core\Data;
use Ushahidi\Core\Entity\ConfigRepository;
use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Traits\FilterRecords;
use Ushahidi\Core\Usecase;

class SearchConfig implements Usecase
{
	// Uses several traits to assign tools. Each of these traits provides a
	// setter method for the tool. For example, the AuthorizerTrait provides
	// a `setAuthorizer` method which only accepts `Authorizer` instances.
	use AuthorizerTrait,
		FormatterTrait;

	// - FilterRecords for setting search parameters
	use FilterRecords;

	/**
	 * @var ConfigRepository
	 */
	protected $repo;

	/**
	 * Inject a repository that can search for config.
	 *
	 * @param  ConfigRepository $repo
	 * @return $this
	 */
	public function setRepository(ConfigRepository $repo)
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
		// Even though this is a "search" usecase, it does not use complex parameters
		// or paging, so there is no need to inject SearchData.
		return false;
	}

	// Usecase
	public function interact()
	{
		// Fetch an empty entity...
		$entity = $this->getEntity();

		// ... verify that the entity can be searched by the current user
		$this->verifySearchAuth($entity);

		// ... get the results of the search
		$results = $this->repo->all($this->getFilter('groups'));

		// ... remove any entities that cannot be seen
		$priv = 'read';
		foreach ($results as $idx => $entity) {
			if (!$this->auth->isAllowed($entity, $priv)) {
				unset($results[$idx]);
			}
		}

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
}
