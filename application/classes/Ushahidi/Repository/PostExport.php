<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Data Export Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\PostExport;
use Ushahidi\Core\Entity\PostExportRepository;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\AdminAccess;

class Ushahidi_Repository_PostExport extends Ushahidi_Repository implements PostExportRepository
{
	use UserContext;
	use AdminAccess;

	protected function getId(Entity $entity)
	{
		$result = $this->selectQuery()
			->where('user_id', '=', $entity->user_id)
			->execute($this->db);
		return $result->get('id', 0);
	}

	protected function getTable()
	{
		return 'post_exports';
	}

	// Ushahidi_Repository
	public function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		$user = $this->getUser();

		// Limit search to user's records unless they are admin
		// or if we get user=me as a search param
		if (! $this->isUserAdmin($user) || $search->user === 'me') {
			$search->user = $this->getUserId();
		}

		foreach ([
			'user'
		] as $fk)
		{
			if ($search->$fk)
			{
				$query->where("post_exports.{$fk}_id", '=', $search->$fk);
			}
		}
	}

	public function getEntity(Array $data = null)
	{
		return new PostExport($data);
	}


	// CreateRepository
	public function create(Entity $entity)
	{
		$id = $this->getId($entity);

		if ($id) {
			// No need to insert a new record.
			// Instead return the id of the Post Export that exists
			return $id;
		}

		$state = [
			'user_id' => $entity->user_id,
			'created' => time(),
		];

		return parent::create($entity->setState($state));
	}

	public function getSearchFields()
	{
		return [
			'user'
		];
	}

	/**
	 * @param $data
	 * @return array
	 */
	public function retrieveColumnNameData($data) {

		/**
		* Tags (native) should not be shown in the CSV Export
		*/
		unset($data['tags']);
		// Set attribute keys
		$attributes = [];
		foreach ($data['values'] as $key => $val)
		{
			$attribute = $this->form_attribute_repo->getByKey($key);
			$attributes[$key] = ['label' => $attribute->label, 'priority'=> $attribute->priority, 'stage' => $attribute->form_stage_id, 'type'=> $attribute->type, 'form_id'=> $data['form_id']];

			// Set attribute names. This is for categories (custom field) to show their label and not the ids
			if ($attribute->type === 'tags') {
			$data['values'][$key] = $this->retrieveTagNames($val);
			}
		}

		$data += ['attributes' => $attributes];


		// Set Set names
		if (!empty($data['sets'])) {
			$data['sets'] = $this->retrieveSetNames($data['sets']);
		}

		// Get contact
		if (!empty($data['contact_id'])) {
			$contact = $this->contact_repo->get($data['contact_id']);
			$data['contact_type'] = $contact->type;
			$data['contact'] = $contact->contact;
		}

		// Set Completed Stage names
		if(!empty($data['completed_stages'])) {
		$data['completed_stages'] = $this->retrieveCompletedStageNames($data['completed_stages']);
		}

		// Set Form name
		if (!empty($data['form_id'])) {
		$form = $this->form_repo->get($data['form_id']);
		$data['form_name'] = $form->name;
		}
		return $data;
	}

	public function retrieveTagNames($tag_ids) {
		$tag_repo = service('repository.tag');
		$names = [];
		foreach($tag_ids as $tag_id) {
		$tag = $tag_repo->get($tag_id);
		array_push($names, $tag->tag);
		}
		return $names;
	}

	public function retrieveSetNames($set_ids) {
		$set_repo = service('repository.set');
		$names = [];
		foreach($set_ids as $set_id) {
		$set = $set_repo->get($set_id);
		array_push($names, $set->name);
		}
		return $names;
	}

	public function retrieveCompletedStageNames($stage_ids) {
		$names = [];
		foreach($stage_ids as $stage_id) {
		$stage = $this->form_stage_repo->get($stage_id);
		array_push($names, $stage->label);
		}
		return $names;
	}
}
