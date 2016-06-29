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
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\RoleRepository;
use Ushahidi\Core\Entity\PostSearchData;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Usecase\Post\UpdatePostRepository;
use Ushahidi\Core\Usecase\Post\UpdatePostTagRepository;

class Ushahidi_Validator_Post_Create extends Validator
{
	protected $repo;
	protected $attribute_repo;
	protected $stage_repo;
	protected $tag_repo;
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
		$this->post_value_factory = $post_value_factory;
		$this->post_value_validator_factory = $post_value_validator_factory;
	}

	protected function getRules()
	{
		$input = $this->validation_engine->getData();
		$parent_id = isset($input['parent_id']) ? $input['parent_id'] : null;
		$type = isset($input['type']) ? $input['type'] : null;
		$form_id = isset($input['form_id']) ? $input['form_id'] : null;

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
				[[$this, 'checkValues'], [':validation', ':value', ':data']],
				[[$this, 'checkRequiredAttributes'], [':validation', ':value', ':data']],
			],
			'tags' => [
				[[$this, 'checkTags'], [':validation', ':value']],
			],
			'user_id' => [
				[[$this->user_repo, 'exists'], [':value']],
				[[$this, 'onlyAuthorOrUserSet'], [':value', ':data']],
			],
			'author_email' => [
				['Valid::email'],
			],
			'author_realname' => [
				['max_length', [':value', 150]],
			],
			'status' => [
				['in_array', [':value', [
					'draft',
					'published'
				]]],
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
				[[$this, 'checkStageInForm'], [':validation', ':value', ':data']],
				[[$this, 'checkRequiredStages'], [':validation', ':value', ':data']]
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

	public function checkValues(Validation $validation, $attributes, $data)
	{
		if (!$attributes)
		{
			return;
		}

		$post_id = ! empty($data['id']) ? $data['id'] : 0;

		foreach ($attributes as $key => $values)
		{
			// Check attribute exists
			$attribute = $this->attribute_repo->getByKey($key, $data['form_id'], true);
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
	 * @param  Array      $data
	 */
	public function checkStageInForm(Validation $validation, $completed_stages, $data)
	{
		if (!$completed_stages)
		{
			return;
		}

		foreach ($completed_stages as $stage_id)
		{
			// Check stage exists in form
			if (! $this->stage_repo->existsInForm($stage_id, $data['form_id']))
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
	 * @param  Array      $data
	 */
	public function checkRequiredStages(Validation $validation, $completed_stages, $data)
	{
		$completed_stages = $completed_stages ? $completed_stages : [];

		// If post is being published
		if ($data['status'] === 'published')
		{
			// Load the required stages
			$required_stages = $this->stage_repo->getRequired($data['form_id']);
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
	 * @param  Array      $data
	 */
	public function checkRequiredAttributes(Validation $validation, $attributes, $data)
	{
		if (empty($data['completed_stages']))
		{
			return;
		}

		// If a stage is being marked completed
		// Check if the required attribute have been completed
		foreach ($data['completed_stages'] as $stage_id)
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
					$validation->error('values', 'attributeRequired', [$attr->label, $stage->label]);
				}
			}
		}
	}

	/**
	 * Check that only author or user info is set
	 * @param  int $user_id
	 * @param  array $data
	 * @return Boolean
	 */
	public function onlyAuthorOrUserSet($user_id, $data)
	{
		return (empty($user_id) OR (empty($data['author_email']) AND empty($data['author_realname'])) );
	}
}
