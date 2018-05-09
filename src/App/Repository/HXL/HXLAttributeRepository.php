<?php

/**
 * Ushahidi HXLTag Repository, using Kohana::$config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository\HXL;

use Ushahidi\Core\Entity\HXL\HXLAttributes;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\HXL\HXLAttributesRepository as HXLAttributeRepositoryContract;
use Ushahidi\Core\Usecase\ReadRepository;
use Ushahidi\Core\Usecase\SearchRepository;
use Ushahidi\App\Repository\OhanzeeRepository;

class HXLAttributeRepository extends OhanzeeRepository implements
	HXLAttributeRepositoryContract,
	SearchRepository,
	ReadRepository
{
	// OhanzeeRepository
	protected function getTable()
	{
		return 'hxl_attributes';
	}

	public function getSearchFields()
    {
		return ['attribute'];
	}

	public function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;
        return $query;
    }

    public function getEntity(array $data = null)
    {
        return new HXLAttributes($data);
    }

	// RoleRepository
	public function getByTagId($tag_id)
	{
		return $this->selectQuery(compact('tag_id'))->execute($this->db)->as_array();
	}
}
