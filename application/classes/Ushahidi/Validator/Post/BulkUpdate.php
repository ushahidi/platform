<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Bulk Update Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\PostSearchData;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PermissionAccess;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\Permissions\ManagePosts;


class Ushahidi_Validator_Post_BulkUpdate extends Validator
{
	use UserContext;

	// Provides `hasPermission`
	use PermissionAccess;

	// Checks if user is Admin
	use AdminAccess;

	// Provides `getPermission`
	use ManagePosts;

	protected $repo;

	protected $default_error_source = 'post';

	/**
	 * Construct
	 *
	 * @param PostRepository                  $repo
	 */
	public function __construct(PostRepository $repo)
	{
		$this->repo = $repo;
	}

	protected function getRules()
	{

		return [
			'actions' => [
				[[$this, 'checkActions'], [':validation',':value']],
			],
			'filters' => [
				[[$this, 'checkFilters'], [':validation',':value']],
			],
		];
	}
	
	public function validateRecords($status, $posts)
	{
		$this->checkPublishedLimit($status, $posts);
		$this->checkRequiredStages($status, $posts);
	}

	protected function checkPublishedLimit($status, $posts)
	{
		$config = \Kohana::$config->load('features.limits');
		
		$validation = $this->validation_engine;

		if ($config['posts'] !== TRUE && $status == 'published') {
			$total_published = $this->repo->getPublishedTotal();

			if (($total_published + sizeof($posts)) >= $config['posts']) {
				$validation->error('status', 'publishedPostsLimitReached');
			}
		}
	}

	/**
	 * Check required stages are completed before publishing
	 *
	 * @param  Validation $validation
	 * @param  Array      $status
	 * @param  Array      $posts
	 */
	protected function checkRequiredStages($status, $posts)
	{
		$validation = $this->validation_engine;

		// If post is being published
		if ($status === 'published')
		{
			//join query to check required stages
			$stages = $this->repo->getPostsIncompleteStages($posts);

			foreach($stages as $stage)
			{
				$validation->error('completed_stages', 'bulkStageRequired', [$stage->post_id, $stage->label]);
			}
		}
	}

	/**
	 * Check that valid filters are included in the payload
	 *
	 * @param  Validation $validation
	 * @param  Array      $filters
	 */
	public function checkFilters(Validation $validation, $filters)
	{
		if (is_array($filters))
		{
			$fields = array_flip($this->repo->getSearchFields());
			$check_filters = array_intersect_key($filters, $fields);

			if (!sizeof($check_filters))
			{
				$validation->error('filters', 'invalidFilters');
			}
		}
	}

	/**
	 * Check is allowed actions are included in the payload
	 *
	 * @param  Validation $validation
	 * @param  Array      $actions
	 */
	public function checkActions(Validation $validation, $actions)
	{
		$status = ['published','draft','archived'];

		if (!is_array($actions) || !isset($actions['status']) || !in_array($actions['status'], $status))
		{
			$validation->error('actions', 'invalidActions');
		}
	}
}
