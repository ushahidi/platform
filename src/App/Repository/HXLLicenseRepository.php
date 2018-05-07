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
use Ushahidi\Core\Usecase\ReadRepository;
use Ushahidi\Core\Usecase\SearchRepository;

class HXLLicenseRepository extends OhanzeeRepository implements
	HXLLicenseRepositoryContract,
    ReadRepository,
    SearchRepository
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

	public function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;
        return $query;
    }

    public function getEntity(array $data = null)
    {
        return new HXLLicense($data);
    }
}
