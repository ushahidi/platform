<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Usecase\Post\UpdatePostRepository;
use Ushahidi\Core\Usecase\Post\UpdatePostTagRepository;

class Ushahidi_Validator_Post_Write implements Validator
{
	protected $repo;
	protected $valid;

	protected $attribute_repo;
	protected $tag_repo;
	protected $post_value_factory;
	protected $post_value_validator_factory;

	/**
	 * Construct
	 *
	 * @param UpdatePostRepository                  $repo
	 * @param FormAttributeRepository               $form_attribute_repo
	 * @param TagRepository                         $tag_repo
	 * @param UserRepository                        $user_repo
	 * @param Ushahidi_Repository_PostValueFactory  $post_value_factory
	 * @param Ushahidi_Validator_Post_ValueFactory  $post_value_validator_factory
	 */
	public function __construct(
			UpdatePostRepository $repo,
			FormAttributeRepository $attribute_repo,
			UpdatePostTagRepository $tag_repo,
			UserRepository $user_repo,
			Ushahidi_Repository_PostValueFactory $post_value_factory,
			Ushahidi_Validator_Post_ValueFactory $post_value_validator_factory)
	{
		$this->repo = $repo;
		$this->attribute_repo = $attribute_repo;
		$this->tag_repo = $tag_repo;
		$this->user_repo = $user_repo;
		$this->post_value_factory = $post_value_factory;
		$this->post_value_validator_factory = $post_value_validator_factory;
	}

	public function check(Entity $entity)
	{
		$this->valid = Validation::factory($entity->getChanged())
			->rules('title', array(
					array('max_length', array(':value', 150)),
				))
			->rules('slug', array(
					array('min_length', array(':value', 2)),
					array('max_length', array(':value', 150)),
					array('alpha_dash', array(':value', TRUE)),
					array([$this->repo, 'isSlugAvailable'], array(':value')),
				))
			->rules('locale', array(
					array('max_length', array(':value', 5)),
					array('alpha_dash', array(':value', TRUE)),
					// @todo check locale is valid
					array(array($this->repo, 'doesTranslationExist'), array(':value', $entity->parent_id, $entity->type))
				))
			->rules('form_id', array(
					array('numeric'),
					array(array($this->repo, 'doesFormExist'), array(':value'))
				))
			->rules('values', [
					[[$this, 'check_values'], [':validation', ':value', ':data']]
				])
			->rules('tags', [
					[[$this, 'check_tags'], [':validation', ':value']]
				])
			->rules('user_id', [
					[[$this->user_repo, 'doesUserExist'], [':value']],
					[[$this, 'onlyAuthorOrUserSet'], [':value', ':data']]
				])
			->rules('author_email', [
					['Valid::email'],
					[[$this->user_repo, 'isUniqueEmail'], [':value']]
				])
			->rules('author_realname', [
					['max_length', [':value', 150]],
				])
			->rules('status', [
					['in_array', [':value', [
						'draft',
						'published'
					]]]
				])
			->rules('type', [
					['in_array', [':value', [
						'report',
						'revision',
						'translation'
					]]]
				]);

		return $this->valid->check();
	}

	public function check_tags(Validation $valid, $tags)
	{
		if (!$tags) {
			return;
		}

		foreach ($tags as $key => $tag)
		{
			if (! $this->tag_repo->doesTagExist($tag))
			{
				$valid->error('tags', 'tagDoesNotExist', [$tag]);
			}
		}
	}

	public function check_values(Validation $valid, $values, $data)
	{
		if (!$values) {
			return;
		}

		$post_id = ! empty($data['id']) ? $data['id'] : 0;

		foreach ($values as $key => $value)
		{
			// Check attribute exists
			$attribute = $this->attribute_repo->getByKey($key, $data['form_id']);
			if (! $attribute->id)
			{
				$valid->error('values', 'attributeDoesNotExist', [$key]);
				return;
			}

			// Are there multiple values? Are they greater than cardinality limit?
			if (count($value) > $attribute->cardinality AND $attribute->cardinality != 0)
			{
				$valid->error('values', 'tooManyValues', [
					$key,
					$attribute->cardinality
				]);
			}

			foreach($value as $k => $v)
			{
				// If id is specified, check post value entry exists
				if (! empty($v['id']))
				{
					// If this is a new post, values should never have 'id'
					if (empty($data['id']))
					{
						$valid->error('values', 'canNotUseExistingValueOnNewPost', [$key, $v['id']]);
					}
					else
					{
						// Check that value with 'id' exists (and is for this post and attribute)
						$value_entity = $this->post_value_factory
							->getRepo($attribute->type)
							->get($v['id'], $data['id'], $attribute->id);

						// Add error if id specified by doesn't exist
						if (! $value_entity)
						{
							$valid->error('values', 'valueDoesNotExist', [$key, $v['id']]);
						}
					}
				}

				// Run checks on individual values type specific validation
				if ($validator = $this->post_value_validator_factory->getValidator($attribute->type))
				{
					if (! $validator->check($v))
					{
						foreach($validator->errors() as $error)
						{
							$valid->error('values', $error, [$key]);
						}
					}
				}
			}
		}

		// Validate required attributes
		$required_attributes = $this->attribute_repo->getRequired($data['form_id']);
		foreach ($required_attributes as $attr)
		{
			if (! array_key_exists($attr->key, $values))
			{
				$valid->error('values', 'attributeRequired', [$attr->key]);
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

	public function errors($from = 'post')
	{
		return $this->valid->errors($from);
	}
}
