<?php

/**
 * Ushahidi Config Repository, using Kohana::$config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository;

use Ushahidi\Core\Entity\HXLLicense;
use Ushahidi\Core\Entity\HXLLicenseRepository as HXLLicenseRepositoryContract;
use Ushahidi\Core\SearchData;

class HXLLicenseRepository extends OhanzeeRepository implements
	HXLLicenseRepositoryContract
{
	// OhanzeeRepository
	protected function getTable()
	{
		return 'hxl_license';
	}

	public function getSearchFields()
	{
		return ['name', 'code'];
	}


	/**
	 * @param SearchData $search
	 * Search by license code
	 */
	public function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;
		if ($search->code) {
			$query->where('code', '=', $search->code);
		}
		if ($search->name) {
			$query->where('name', '=', $search->name);
		}
		return $query;
	}

	public function getEntity(array $data = null)
	{
		return new HXLLicense($data);
	}
}
