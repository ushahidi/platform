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
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\HXL\HXLTag;
use Ushahidi\Core\Entity\HXL\HXLTagRepository as HXLTagRepositoryContract;
use Ushahidi\Core\Usecase\ReadRepository;
use Ushahidi\Core\Usecase\SearchRepository;
use Ushahidi\App\Repository\OhanzeeRepository;
use Ohanzee\DB;

class HXLTagRepository extends OhanzeeRepository implements
	HXLTagRepositoryContract,
	SearchRepository,
	ReadRepository
{
	private $tags_attributes;

	public function __construct(Database $db)
	{
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

	/**
	 * @param array|null $data
	 * @return \Ushahidi\App\Repository\Ushahidi\Core\Entity|HXLTag|\Ushahidi\Core\Usecase\Entity
	 */
	public function getEntity(array $data = null)
	{
		if (!$this->tags_attributes) {
			$this->tags_attributes = $this->getAllHXLAttributes();
		}

		if ($data) {
			$data['form_attribute_types'] = $this->getFormAttributeTypes($data['id']);
			$data['hxl_attributes'] = array_filter($this->tags_attributes, function ($tag_attributes) use ($data) {
				return $tag_attributes['hxl_tag_id'] === $data['id'];
			});
		}
		return new HXLTag($data);
	}

	/**
	 * @param $hxl_tag_id
	 */
	protected function getAllHXLAttributes()
	{
		return DB::select(
			['hxl_tags.id', 'hxl_tag_id'],
			'hxl_attributes.id',
			'hxl_attributes.attribute',
			'hxl_attributes.description'
		)
			->from('hxl_tags')
			->join('hxl_tag_attributes')
			->on('hxl_tags.id', '=', 'hxl_tag_attributes.tag_id')
			->join('hxl_attributes')
			->on('hxl_tag_attributes.attribute_id', '=', 'hxl_attributes.id')->execute($this->db)->as_array();
	}

	/**
	 * Get all attribute types that can be matched to this hxl tag
	 * @param $hxl_tag_id
	 * @return mixed
	 */
	protected function getFormAttributeTypes($hxl_tag_id)
	{
		return DB::select('form_attribute_type')
			->from('hxl_attribute_type_tag')
			->where('hxl_tag_id', '=', $hxl_tag_id)
			->execute($this->db)->as_array();
	}
}
