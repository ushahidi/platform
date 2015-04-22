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
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Entity\FormRepository;
use Ushahidi\Core\Entity\RoleRepository;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Usecase\Post\UpdatePostRepository;
use Ushahidi\Core\Usecase\Post\UpdatePostTagRepository;

class Ushahidi_Validator_Post_Create extends Validator
{
	protected $repo;
	protected $attribute_repo;
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
	 * @param Ushahidi_Repository_PostValueFactory  $post_value_factory
	 * @param Ushahidi_Validator_Post_ValueFactory  $post_value_validator_factory
	 */
	public function __construct(
		UpdatePostRepository $repo,
		FormAttributeRepository $attribute_repo,
		UpdatePostTagRepository $tag_repo,
		UserRepository $user_repo,
		FormRepository $form_repo,
		RoleRepository $role_repo,
		Ushahidi_Repository_PostValueFactory $post_value_factory,
		Ushahidi_Validator_Post_ValueFactory $post_value_validator_factory)
	{
		$this->repo = $repo;
		$this->attribute_repo = $attribute_repo;
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
				[[$this->user_repo, 'isUniqueEmail'], [':value']],
			],
			'author_realname' => [
				['max_length', [':value', 150]],
			],
			'status' => [
				['in_array', [':value', [
					'draft',
					'published'
				]]],
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
			]
		];
	}

	public function checkTags(Validation $validation, $tags)
	{
		if (!$tags) {
			return;
		}

		foreach ($tags as $key => $tag)
		{
			if (! $this->tag_repo->doesTagExist($tag))
			{
				$validation->error('tags', 'tagDoesNotExist', [$tag]);
			}
		}
	}

	public function checkValues(Validation $validation, $attributes, $data)
	{
		if (!$attributes) {
			return;
		}

		$post_id = ! empty($data['id']) ? $data['id'] : 0;

		foreach ($attributes as $key => $values)
		{
			// Check attribute exists
			$attribute = $this->attribute_repo->getByKey($key, $data['form_id']);
			if (! $attribute->id)
			{
				$validation->error('values', 'attributeDoesNotExist', [$key]);
				return;
			}

			// Are there multiple values? Are they greater than cardinality limit?
			if (count($values) > $attribute->cardinality AND $attribute->cardinality != 0)
			{
				$validation->error('values', 'tooManyValues', [
					$key,
					$attribute->cardinality
				]);
			}

			// Run checks on individual values type specific validation
			if ($validator = $this->post_value_validator_factory->getValidator($attribute->type))
			{
				if (!is_array($values)) {
					$validation->error('values', 'notAnArray', [$key]);
				}
				elseif ($error = $validator->check($values)) {
					$validation->error('values', $error, [$key]);
				}
			}
		}

		// Validate required attributes
		$this->checkRequiredAttributes($validation, $attributes, $data);
	}

	protected function checkRequiredAttributes(Validation $validation, $attributes, $data)
	{
		$required_attributes = $this->attribute_repo->getRequired($data['form_id']);
		foreach ($required_attributes as $attr)
		{
			if (!array_key_exists($attr->key, $attributes))
			{
				$validation->error('values', 'attributeRequired', [$attr->key]);
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
