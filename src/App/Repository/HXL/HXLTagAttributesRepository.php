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

use Ohanzee\Database;
use Ohanzee\DB;
use Ushahidi\Core\Entity\HXL\HXLTag;
use Ushahidi\Core\Entity\HXL\HXLTagAttributes;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\HXL\HXLTagAttributesRepository as HXLTagAttributesRepositoryContract;
use Ushahidi\Core\Usecase\ReadRepository;
use Ushahidi\Core\Usecase\SearchRepository;
use Ushahidi\App\Repository\OhanzeeRepository;

class HXLTagAttributesRepository extends OhanzeeRepository implements
	HXLTagAttributesRepositoryContract,
	SearchRepository,
	ReadRepository
{

	public function __construct(
		Database $db
	) {
		parent::__construct($db);
	}

	// OhanzeeRepository
	protected function getTable()
	{
		return 'hxl_attribute_type_tag';
	}

	public function getSearchFields()
    {
		return ['form_attribute_type', 'hxl_tag_id'];
	}

	public function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;
        return $query;
    }

	// OhanzeeRepository
	public function getEntity(array $data = null)
	{
		return new HXLTagAttributes($data);
	}

	public function getTypesByTagId($hxl_tag_id)
	{
		return DB::select('form_attribute_type')
			->from('hxl_attribute_type_tag')
			->where('hxl_tag_id', '=', $hxl_tag_id)
			->execute($this->db)->as_array();
	}
}
