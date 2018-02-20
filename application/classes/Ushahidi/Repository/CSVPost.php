<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\FormRepository;
use Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\Core\Entity\FormStageRepository;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Entity\PostLock;
use Ushahidi\Core\Entity\PostLockRepository;
use Ushahidi\Core\Entity\PostValueContainer;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Usecase\Post\StatsPostRepository;
use Ushahidi\Core\Usecase\Post\UpdatePostRepository;
use Ushahidi\Core\Usecase\Set\SetPostRepository;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\Permissions\ManagePosts;
use Ushahidi\Core\Tool\Permissions\AclTrait;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Tool\Permissions\Permissionable;
use Ushahidi\Core\Traits\PostValueRestrictions;
use Ushahidi\Core\Entity\ContactRepository;

use Aura\DI\InstanceFactory;

use League\Event\ListenerInterface;
use Ushahidi\Core\Traits\Event;

class Ushahidi_Repository_CSVPost extends Ushahidi_Repository_Post
{
	// Ushahidi_Repository
	public function getEntity(Array $data = null)
	{
		// Ensure we are dealing with a structured Post

		$user = $this->getUser();
		if ($data['form_id'])
		{

			if ($this->canUserReadPostsValues(new Post($data), $user, $this->form_repo)) {
				$this->restricted = false;
			}
			// Get Hidden Stage Ids to be excluded from results
			$status = $data['status'] ? $data['status'] : '';
			$this->exclude_stages = $this->form_stage_repo->getHiddenStageIds($data['form_id'], $data['status']);

		}

		if (!empty($data['id']))
		{
			$data += [
				'values' => $this->getPostValues($data['id']),
				// Continued for legacy
				'tags'   => $this->getTagsForPost($data['id'], $data['form_id']),
				'sets' => $this->getSetsForPost($data['id']),
				'completed_stages' => $this->getCompletedStagesForPost($data['id']),
				'lock' => NULL,
			];


			if ($this->canUserSeePostLock(new Post($data), $user)) {
				$data['lock'] = $this->getHydratedLock($data['id']);
			}
		}
		// NOTE: This and the restriction above belong somewhere else,
		// ideally in their own step
		// Check if author information should be returned
		if ($data['author_realname'] || $data['user_id'] || $data['author_email'])
		{


			if (!$this->canUserSeeAuthor(new Post($data), $this->form_repo, $user))
			{
				unset($data['author_realname']);
				unset($data['author_email']);
				unset($data['user_id']);
			}
		}

		return new Post($data);
	}

	/**
	 * Get tags for a post
	 * @param  int   $id  post id
	 * @return array      tag ids for post
	 */
	private function getTagsForPost($id, $form_id)
	{
		list($attr_id, $attr_key) = $this->getFirstTagAttr($form_id);

		$result = DB::select('tag_id')->from('posts_tags')
			->where('post_id', '=', $id)
			->where('form_attribute_id', '=', $attr_id)
			->execute($this->db);
		return $result->as_array(NULL, 'tag_id');
	}

	/**
	 * Get sets for a post
	 * @param  int   $id  post id
	 * @return array      set ids for post
	 */
	private function getSetsForPost($id)
	{
		$result = DB::select('set_id')->from('posts_sets')
			->where('post_id', '=', $id)
			->execute($this->db);
		return $result->as_array(NULL, 'set_id');
	}

	protected function getPostValues($id)
	{

		// Get all the values for the post. These are the EAV values.
		$values = $this->post_value_factory
			->proxy($this->include_value_types)
			->getAllForPost($id, $this->include_attributes, $this->exclude_stages, $this->restricted);

		$output = [];
		foreach ($values as $value) {
			if (empty($output[$value->key])) {
				$output[$value->key] = [];
			}
			if (is_array($value->value) && isset($value->value['o_filename'])) {
				$output[$value->key][] = $value->value['o_filename'];
			} else if ($value->value !== NULL) {
				$output[$value->key][] = $value->value;
			}
		}
		return $output;
	}
}
