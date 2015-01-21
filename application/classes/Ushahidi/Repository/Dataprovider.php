<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Data Provider Repository, using DataProvider factory
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\DataProvider as DataProviderEntity;
use Ushahidi\Core\Entity\DataProviderRepository;
use Ushahidi\Core\Usecase\ReadRepository;
use Ushahidi\Core\Usecase\SearchRepository;
use Ushahidi\Core\Traits\CollectionLoader;
use Ushahidi\Core\Exception\NotFoundException;

class Ushahidi_Repository_Dataprovider implements
	ReadRepository,
	SearchRepository,
	DataProviderRepository
{

	use CollectionLoader;

	// ReadRepository
	public function getEntity(Array $data = null)
	{
		return new DataProviderEntity($data);
	}

	/**
	 * Get all enabled providers, with their configuration data.
	 * @return Array
	 */
	protected function getAllProviders($enabled = false)
	{
		if ($enabled)
		{
			// Returns all *enabled* providers.
			return \DataProvider::get_enabled_providers();
		}
		else
		{
			// Returns all providers, even if they are disabled.
			return \DataProvider::get_providers();
		}
	}

	// DataProviderRepository
	public function all($enabled = false)
	{
		$providers = $this->getAllProviders($enabled);
		return $this->getCollection($providers);
	}

	// ReadRepository
	public function get($provider)
	{
		$providers = $this->getAllProviders();

		if (!isset($providers[$provider])) {
			return new DataProviderEntity([]);
		}

		return new DataProviderEntity($providers[$provider]);
	}

	// SearchRepository
	public function getSearchFields()
	{
		return ['type'];
	}

	// SearchRepository
	public function setSearchParams(SearchData $search)
	{
		$this->search_params = $search;
	}

	// SearchRepository
	public function getSearchResults()
	{
		$providers = $this->getAllProviders();

		foreach ($providers as $name => $info) {
			if ($this->search_params->type) {
				if (empty($info['services'][$this->search_params->type])) {
					// Provider does not offer this type of service, skip it.
					unset($providers[$name]);
				}
			}
		}

		$this->search_total = count($providers);

		return $this->getCollection($providers);
	}

	// SearchRepository
	public function getSearchTotal()
	{
		return $this->search_total;
	}
}
