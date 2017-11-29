<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Create Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\Core\Entity\FormStageRepository;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Entity\FormRepository;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\RoleRepository;
use Ushahidi\Core\Entity\PostLockRepository;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Tool\Permissions\AclTrait;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\Permissions\ManagePosts;
use Ushahidi\Core\Usecase\Post\UpdatePostRepository;
use Ushahidi\Core\Usecase\Post\UpdatePostTagRepository;

class Ushahidi_Validator_Post_Create extends Validator
{
	use UserContext;

	// Provides `acl`
	use AclTrait;

	// Checks if user is Admin
	use AdminAccess;

	protected $repo;
	protected $attribute_repo;
	protected $stage_repo;
	protected $tag_repo;
	protected $post_lock_repo;
	protected $user_repo;
	protected $post_value_factory;
	protected $post_value_validator_factory;

	protected $default_error_source = 'post';

	/**
	 * Construct
	 *
	 * @param UpdatePostRepository                  $repo
	 * @param FormAttributeRepository               $form_attribute_repo
	 * @param TagRepository                         $tag_repo
	 * @param UserRepository                        $user_repo
	 * @param FormRepository                        $form_repo
	 * @param RoleRepository                        $role_repo
	 * @param Ushahidi_Repository_Post_ValueFactory  $post_value_factory
	 * @param Ushahidi_Validator_Post_ValueFactory  $post_value_validator_factory
	 */
	public function __construct(
		UpdatePostRepository $repo,
		FormAttributeRepository $attribute_repo,
		FormStageRepository $stage_repo,
		UpdatePostTagRepository $tag_repo,
		UserRepository $user_repo,
		FormRepository $form_repo,
		RoleRepository $role_repo,
		PostLockRepository $post_lock_repo,
		Ushahidi_Repository_Post_ValueFactory $post_value_factory,
		Ushahidi_Validator_Post_ValueFactory $post_value_validator_factory)
	{
		$this->repo = $repo;
		$this->attribute_repo = $attribute_repo;
		$this->stage_repo = $stage_repo;
		$this->tag_repo = $tag_repo;
		$this->user_repo = $user_repo;
		$this->form_repo = $form_repo;
		$this->role_repo = $role_repo;
		$this->post_lock_repo = $post_lock_repo;
		$this->post_value_factory = $post_value_factory;
		$this->post_value_validator_factory = $post_value_validator_factory;
	}

	protected function getRules()
	{
		// Hack to avoid Kohana Validation trying to convert post_date into a string
		$fullData = $this->validation_engine->getFullData();
		if ($fullData['post_date']) {
			$fullData['post_date'] = $fullData['post_date']->format('Y-m-d H:i:s');
			$this->validation_engine->setFullData($fullData);
		}
		// End hack

		$parent_id = $this->validation_engine->getFullData('parent_id');
		$type = $this->validation_engine->getFullData('type');

		return [
			'title' => [
				['max_length', [':value', 150]],
			],
			'slug' => [
				['min_length', [':value', 2]],
				['max_length', [':value', 150]],
				['alpha_dash', [':value', TRUE]],
				[[$this->repo, 'isSlugAvailable'], [':value']],
			],
			'locale' => [
				['max_length', [':value', 5]],
				['alpha_dash', [':value', TRUE]],
				// @todo check locale is valid
				// @todo if the translation exists and we're performing an Update,
				//       passing locale should not throw an error
				[[$this->repo, 'doesTranslationExist'], [
					':value', $parent_id, $type
				]],
			],
			'form_id' => [
				['numeric'],
				[[$this->form_repo, 'exists'], [':value']],
			],
			'values' => [
				[[$this, 'checkValues'], [':validation', ':value', ':fulldata']],
				[[$this, 'checkRequiredPostAttributes'], [':validation', ':value', ':fulldata']],
				[[$this, 'checkRequiredTaskAttributes'], [':validation', ':value', ':fulldata']],
			],
			'post_date' => [
				[[$this, 'validDate'], [':value']],
			],
			'tags' => [
				[[$this, 'checkTags'], [':validation', ':value']],
			],
			'user_id' => [
				[[$this->user_repo, 'exists'], [':value']],
				[[$this, 'onlyAuthorOrUserSet'], [':value', ':fulldata']],
			],
			'author_email' => [
				['Valid::email'],
			],
			'author_realname' => [
				['max_length', [':value', 150]],
			],
			'status' => [
				['in_array', [':value', [
					'published',
					'draft',
					'archived'
				]]],
				[[$this, 'checkApprovalRequired'], [':validation', ':value', ':fulldata']],
				[[$this, 'checkPublishedLimit'], [':validation', ':value']]
			],
			'type' => [
				['in_array', [':value', [
					'report',
					'revision',
					'translation'
				]]],
			],
			'published_to' => [
				[[$this->role_repo, 'exists'], [':value']],
			],
			'completed_stages' => [
				[[$this, 'checkStageInForm'], [':validation', ':value', ':fulldata']],
				[[$this, 'checkRequiredStages'], [':validation', ':fulldata']]
			]
		];
	}

	public function checkPublishedLimit (Validation $validation, $status)
	{
		$config = \Kohana::$config->load('features.limits');

		if ($config['posts'] !== TRUE && $status == 'published') {
			$total_published = $this->repo->getPublishedTotal();

			if ($total_published >= $config['posts']) {
				$validation->error('status', 'publishedPostsLimitReached');
			}
		}
	}

	public function checkApprovalRequired (Validation $validation, $status, $fullData)
	{
		// Status hasn't changed, moving on
		if (!$status) {
			return;
		}

		if ($status === 'draft' && !isset($fullData['id'])) {
			return;
		}

		$user = $this->getUser();
		// Do we have permission to publish this post?
		$userCanChangeStatus = ($this->isUserAdmin($user) or $this->acl->hasPermission($user, Permission::MANAGE_POSTS));
		// .. if yes, any status is ok.
		if ($userCanChangeStatus) {
			return;
		}

		$requireApproval = $this->repo->doesPostRequireApproval($fullData['form_id']);

		// Are we trying to change publish a post that requires approval?
		if ($requireApproval && $status !== 'draft') {
			$validation->error('status', 'postNeedsApprovalBeforePublishing');
		// Are we trying to unpublish or archive an auto-approved post?
		} elseif (!$requireApproval && $status !== 'published') {
			$validation->error('status', 'postCanOnlyBeUnpublishedByAdmin');
		}
	}

	public function checkTags(Validation $validation, $tags)
	{
		if (!$tags) {
			return;
		}

		foreach ($tags as $key => $tag)
		{
			if (is_array($tag)) {
				$tag = $tag['id'];
			}

			if (! $this->tag_repo->doesTagExist($tag))
			{
				$validation->error('tags', 'tagDoesNotExist', [$tag]);
			}
		}
	}

	public function checkValues(Validation $validation, $attributes, $fullData)
	{

		$attributes = !empty($fullData['values']) ? $fullData['values'] : [];
		if (!$attributes)
		{
			return;
		}

		$post_id = ! empty($fullData['id']) ? $fullData['id'] : 0;

		foreach ($attributes as $key => $values)
		{
			// Check attribute exists
			$attribute = $this->attribute_repo->getByKey($key, $fullData['form_id'], true);
			if (! $attribute->id)
			{
				$validation->error('values', 'attributeDoesNotExist', [$key]);
				return;
			}

			// Are there multiple values? Are they greater than cardinality limit?
			if (count($values) > $attribute->cardinality AND $attribute->cardinality != 0)
			{
				$validation->error('values', 'tooManyValues', [
					$attribute->label,
					$attribute->cardinality
				]);
			}

			// Run checks on individual values type specific validation
			if ($validator = $this->post_value_validator_factory->getValidator($attribute->type))
			{
				// Pass attribute config to the validator
				$validator->setConfig($attribute->config);

				if (!is_array($values))
				{
					$validation->error('values', 'notAnArray', [$attribute->label]);
				}
				elseif ($error = $validator->check($values))
				{
					$validation->error('values', $error, [$attribute->label, $values]);
				}
			}
		}
	}

	/**
	 * Check completed stages actually exist in form
	 *
	 * @param  Validation $validation
	 * @param  Array      $attributes
	 * @param  Array      $fullData
	 */
	public function checkStageInForm(Validation $validation, $completed_stages, $fullData)
	{
		if (!$completed_stages)
		{
			return;
		}

		foreach ($completed_stages as $stage_id)
		{
			// Check stage exists in form
			if (! $this->stage_repo->existsInForm($stage_id, $fullData['form_id']))
			{
				$validation->error('completed_stages', 'stageDoesNotExist', [$stage_id]);
				return;
			}
		}
	}

	/**
	 * Check required stages are completed before publishing
	 *
	 * @param  Validation $validation
	 * @param  Array      $attributes
	 * @param  Array      $fullData
	 */
	public function checkRequiredStages(Validation $validation, $fullData)
	{
		$completed_stages = !empty($fullData['completed_stages']) ? $fullData['completed_stages'] : [];

		// If post is being published
		if ($fullData['status'] === 'published')
		{
			// Load the required stages
			$required_stages = $this->stage_repo->getRequired($fullData['form_id']);
			foreach ($required_stages as $stage)
			{
				// Check the required stages have been completed
				if (! in_array($stage->id, $completed_stages))
				{
					// If its not completed, add a validation error
					$validation->error('completed_stages', 'stageRequired', [$stage->label]);
				}
			}
		}
	}

	/**
	 * Check required attributes are completed before completing stages
	 *
	 * @param  Validation $validation
	 * @param  Array      $attributes
	 * @param  Array      $fullData
	 */
	public function checkRequiredPostAttributes(Validation $validation, $attributes, $fullData)
	{
		// Get the post stage
		$stage = $this->stage_repo->getPostStage($fullData['form_id']);

		// Load the required attributes
		$required_attributes = $this->attribute_repo->getRequired($stage->id);

		foreach ($required_attributes as $attr)
		{
			// Post has two special required attributes Title and Desription
			// these are checked separately and skipped here.
			// TODO: Refactor Title and Description to be handled as Post Values
			if (!in_array($attr->type, ['title', 'description']) && !array_key_exists($attr->key, $attributes))
			{
				// If a required attribute isn't completed, throw an error
				$validation->error('values', 'postAttributeRequired', [$attr->label, $stage->label]);
			}
		}
	}

	/**
	 * Check required attributes are completed before completing stages
	 *
	 * @param  Validation $validation
	 * @param  Array      $attributes
	 * @param  Array      $fullData
	 */
	public function checkRequiredTaskAttributes(Validation $validation, $attributes, $fullData)
	{
		if (empty($fullData['completed_stages']))
		{
			return;
		}

		// If a stage is being marked completed
		// Check if the required attribute have been completed
		foreach ($fullData['completed_stages'] as $stage_id)
		{
			// Load the required attributes
			$required_attributes = $this->attribute_repo->getRequired($stage_id);

			// Check each attribute has been completed
			foreach ($required_attributes as $attr)
			{
				if (!array_key_exists($attr->key, $attributes))
				{
					$stage = $this->stage_repo->get($stage_id);
					// If a required attribute isn't completed, throw an error
					$validation->error('values', 'taskAttributeRequired', [$attr->label, $stage->label]);
				}
			}
		}
	}

	/**
	 * Check that only author or user info is set
	 * @param  int $user_id
	 * @param  array $fullData
	 * @return Boolean
	 */
	public function onlyAuthorOrUserSet($user_id, $fullData)
	{
		return (empty($user_id) OR (empty($fullData['author_email']) AND empty($fullData['author_realname'])) );
	}

	public function validDate($str)
	{
		if ($str instanceof \DateTimeInterface) {
			return true;
		}
		return (strtotime($str) !== FALSE);
	}
}
