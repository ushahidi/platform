<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Set Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Set;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Tool\JsonTranscode;

class Ushahidi_Repository_Set extends Ushahidi_Repository
{
	protected $json_transcoder;
	protected $json_properties = ['filter', 'view_options', 'visible_to'];

	public function setTranscoder(JsonTranscode $transcoder)
	{
		$this->json_transcoder = $transcoder;
	}

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'sets';
	}

	// Ushahidi_Repository
	public function getEntity(Array $data = null)
	{
		return new Set($data);
	}

	// CreateRepository
	public function create(Entity $entity) {
		$record = array_filter($this->json_transcoder->encode(
			$entity->asArray(),
			$this->json_properties
		));
		$record['created'] = time();
		return $this->executeInsert($this->removeNullValues($record));

	}

	// UpdateRepository
	public function update(Entity $entity) {
		return parent::update($entity->setState(['updated' => time()]));
	}

	// SearchRepository
	public function getSearchFields()
	{
		return [
			'user_id',
			'q', /* LIKE name */
			'search',
			'featured',
		];
	}

	// SearchRepository
	public function setSearchParams(SearchData $search)
	{
		// Overriding so we can alter sorting logic
		// @todo make it easier to override just sorting

		$this->search_query = $this->selectQuery();

		$sorting = $search->getSorting();

		// Always return featured sets first
		// @todo make this optional
		$this->search_query->order_by('sets.featured', 'DESC');

		if (!empty($sorting['orderby'])) {
			$this->search_query->order_by(
				$this->getTable() . '.' . $sorting['orderby'],
				Arr::get($sorting, 'order')
			);
		}

		if (!empty($sorting['offset'])) {
			$this->search_query->offset($sorting['offset']);
		}

		if (!empty($sorting['limit'])) {
			$this->search_query->limit($sorting['limit']);
		}

		// apply the unique conditions of the search
		$this->setSearchConditions($search);
	}

	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		$sets_query = $this->search_query;

		if ($search->q)
		{
			$sets_query->where('name', 'LIKE', "%{$search->q}%");
		}

		if ($search->search !== null)
		{
			$sets_query->where('search', '=', (int)$search->search);
		}

		if ($search->featured !== null)
		{
			$sets_query->where('featured', '=', (int)$search->featured);
		}

		if ($search->user_id)
		{
			$sets_query->where('user_id', '=', $search->user_id);
		}
	}
}
