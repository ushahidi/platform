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
use Ushahidi\Core\Entity\HXL\HXLAttributes;
use Ushahidi\Core\Entity\HXL\HXLTagAttributes;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\HXL\HXLTag;
use Ushahidi\Core\Entity\HXL\HXLTagRepository as HXLTagRepositoryContract;
use Ushahidi\Core\Usecase\ReadRepository;
use Ushahidi\Core\Usecase\SearchRepository;
use Ushahidi\App\Repository\OhanzeeRepository;
use Ushahidi\Core\Entity\HXL\HXLAttributesRepository;

class HXLTagRepository extends OhanzeeRepository implements
	HXLTagRepositoryContract,
	SearchRepository,
	ReadRepository
{
	protected $hxl_attribute_repo;
	protected $hxl_tag_attribute_repo;
	public function __construct(
		Database $db,
		HXLAttributesRepository $hxl_attribute_repo,
		HXLTagAttributesRepository $hxl_tag_attribute_repo
	) {
		$this->hxl_attribute_repo = $hxl_attribute_repo;
		$this->hxl_tag_attribute_repo = $hxl_tag_attribute_repo;

		parent::__construct($db);
	}

	// OhanzeeRepository
	protected function getTable()
	{
		return 'hxl_tags';
	}

	public function getSearchFields()
    {
		return ['tag_name'];
	}

	public function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;
        return $query;
    }

    public function getEntity(array $data = null)
    {
    	if ($data) {
			$data['form_attribute_types'] = array_flatten($this->getTypes($data['id']));
			$data['hxl_attributes'] = $this->getHydratedAttributes($data['id']);
    	}
		return new HXLTag($data);
    }

    protected function getHydratedAttributes($hxl_tag_id)
	{
		return $this->hxl_attribute_repo->getByTagId($hxl_tag_id);
	}

	protected function getTypes($hxl_tag_id)
    {
		return $this->hxl_tag_attribute_repo->getTypesByTagId($hxl_tag_id);
	}
}
