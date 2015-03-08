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
use Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Entity\PostValueContainer;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\PostSearchData;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Usecase\Post\UpdatePostRepository;
use Ushahidi\Core\Usecase\Post\UpdatePostTagRepository;

use Aura\DI\InstanceFactory;

class Ushahidi_Repository_Post extends Ushahidi_Repository implements
	PostRepository,
	UpdatePostRepository
{
	protected $form_attribute_repo;
	protected $post_value_factory;
	protected $bounding_box_factory;
	protected $tag_repo;

	protected $include_value_types = [];
	protected $include_attributes = [];

	/**
	 * Construct
	 * @param Database                              $db
	 * @param FormAttributeRepository               $form_attribute_repo
	 * @param Ushahidi_Repository_PostValueFactory  $post_value_factory
	 * @param Aura\DI\InstanceFactory               $bounding_box_factory
	 */
	public function __construct(
			Database $db,
			FormAttributeRepository $form_attribute_repo,
			Ushahidi_Repository_PostValueFactory $post_value_factory,
			InstanceFactory $bounding_box_factory,
			UpdatePostTagRepository $tag_repo
		)
	{
		parent::__construct($db);

		$this->form_attribute_repo = $form_attribute_repo;
		$this->post_value_factory = $post_value_factory;
		$this->bounding_box_factory = $bounding_box_factory;
		$this->tag_repo = $tag_repo;
	}

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'posts';
	}

	// Ushahidi_Repository
	public function getEntity(Array $data = null)
	{
		if (!empty($data['id']))
		{
			$data += [
				'values' => $this->getPostValues($data['id']),
				'tags'   => $this->getTagsForPost($data['id']),
			];
		}

		return new Post($data);
	}

	protected function getPostValues($id)
	{
		// Get all the values for the post. These are the EAV values.
		$values = $this->post_value_factory
			->proxy($this->include_value_types)
			->getAllForPost($id, $this->include_attributes);

		$output = [];
		foreach ($values as $value) {
			if (empty($output[$value->key])) {
				$output[$value->key] = [];
			}
			if ($value->value !== NULL) {
				$output[$value->key][] = $value->value;
			}
		}
		return $output;
	}

	// Ushahidi_Repository
	public function getSearchFields()
	{
		return [
			'status', 'type', 'locale', 'slug', 'user',
			'parent', 'form', 'set', 'q', /* LIKE title, content */
			'created_before', 'created_after',
			'updated_before', 'updated_after',
			'bbox', 'tags', 'values',
			'center_point', 'within_km',
			'include_types', 'include_attributes'
		];
	}

	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		if ($search->include_types)
		{
			if (is_array($search->include_types))
			{
				$this->include_value_types = $search->include_types;
			}
			else
			{
				$this->include_value_types = explode(',', $search->include_types);
			}
		}

		if ($search->include_attributes)
		{
			if (is_array($search->include_attributes))
			{
				$this->include_attributes = $search->include_attributes;
			}
			else
			{
				$this->include_attributes = explode(',', $search->include_attributes);
			}
		}

		$query = $this->search_query;

		if (!$search->status)
		{
			// Only show published by default
			$query->where('status', '=', 'published');
		}
		elseif ($search->status !== 'all')
		{
			$query->where('status', '=', $search->status);
		}

		$table = $this->getTable();

		foreach (['type', 'locale', 'slug'] as $key)
		{
			if ($search->$key)
			{
				$query->where("$table.$key", '=', $search->$key);
			}
		}

		foreach (['user', 'parent', 'form'] as $key)
		{
			if ($search->$key)
			{
				$query->where("$table.{$key}_id", '=', $search->$key);
			}
		}

		if ($search->q)
		{
			// search terms are all wrapped as a series of OR conditions
			$query->and_where_open();

			if (ctype_digit($search->q)) {
				// possibly searching for a specific id
				$query->or_where('id', '=', $search->q);
			}

			// or possible text searching in title / content
			$query
				->where('title', 'LIKE', "%$search->q%")
				->or_where('content', 'LIKE', "%$search->q%");

			$query->and_where_close();
		}

		// date chcks
		if ($search->created_after)
		{
			$created_after = strtotime($search->created_after);
			$query->where('created', '>=', $created_after);
		}

		if ($search->created_before)
		{
			$created_before = strtotime($search->created_before);
			$query->where('created', '<=', $created_before);
		}

		if ($search->updated_after)
		{
			$updated_after = strtotime($search->updated_after);
			$query->where('updated', '>=', $updated_after);
		}

		if ($search->updated_before)
		{
			$updated_before = strtotime($search->updated_before);
			$query->where('updated', '<=', $updated_before);
		}

		// Bounding box search
		// Create geometry from bbox (or create bbox from center & radius)
		$bounding_box = null;
		if ($search->bbox) {
			$bounding_box = $this->createBoundingBoxFromCSV($search->bbox);
		} else if ($search->center_point && $search->within_km) {
			$bounding_box = $this->createBoundingBoxFromCenter(
				$search->center_point, $search->within_km
			);
		}

		if ($bounding_box) {
			$query
				->join([
					$this->getBoundingBoxSubquery($bounding_box), 'Filter_BBox'
				], 'INNER')
				->on('posts.id', '=', 'Filter_BBox.post_id')
				;
		}

		// Filter by tag
		if ($search->tags)
		{
			if (isset($search->tags['any']))
			{
				$tags = $search->tags['any'];
				if (!is_array($tags)) {
					$tags = explode(',', $tags);
				}

				$query
					->join('posts_tags')->on('posts.id', '=', 'posts_tags.post_id')
					->where('tag_id', 'IN', $tags);
			}
			elseif (isset($search->tags['all']))
			{
				$tags = $search->tags['all'];
				if (!is_array($tags)) {
					$tags = explode(',', $tags);
				}

				foreach ($tags as $tag)
				{
					$sub = DB::select('post_id')
						->from('posts_tags')
						->where('tag_id', '=', $tag);

					$query
						->where('posts.id', 'IN', $sub);
				}
			}
			else
			{
				$tags = $search->tags;
				if (!is_array($tags)) {
					$tags = explode(',', $tags);
				}

				$query
					->join('posts_tags')->on('posts.id', '=', 'posts_tags.post_id')
					->where('tag_id', 'IN', $tags);
			}
		}

		// Filter by set
		if ($search->set)
		{
			$query
				->join('posts_sets', 'INNER')->on('posts.id', '=', 'posts_sets.post_id')
				->where('posts_sets.set_id', '=', $search->set);
		}

		// Attributes
		if ($search->values)
		{
			foreach ($search->values as $key => $value)
			{
				$attribute = $this->form_attribute_repo->getByKey($key);

				$sub = $this->post_value_factory
					->getRepo($attribute->type)
					->getValueQuery($attribute->id, $value);

				$query
					->join([$sub, 'Filter_'.ucfirst($key)], 'INNER')
					->on('posts.id', '=', 'Filter_'.ucfirst($key).'.post_id');
			}
		}
	}

	// SearchRepository
	public function getSearchTotal()
	{
		// Assume we can simply count the results to get a total
		$query = $this->getSearchQuery(true)
			->select([DB::expr('COUNT(DISTINCT posts.id)'), 'total']);

		// Fetch the result and...
		$result = $query->execute($this->db);

		// ... return the total.
		return (int) $result->get('total', 0);
	}

	// PostRepository
	public function getByIdAndParent($id, $parent_id)
	{
		return $this->getEntity($this->selectOne(compact('id', 'parent_id')));
	}

	// PostRepository
	public function getByLocale($locale, $parent_id)
	{
		return $this->getEntity($this->selectOne(compact('locale', 'parent_id')));
	}

	/**
	 * Return a Bounding Box given a CSV of west,north,east,south points
	 *
	 * @param  string $csv 'west,north,east,south'
	 * @return Util_BoundingBox
	 */
	private function createBoundingBoxFromCSV($csv)
	{
		list($bb_west, $bb_north, $bb_east, $bb_south)
				= array_map('floatval', explode(',', $csv))
				;

		$bounding_box_factory = $this->bounding_box_factory;
		return $bounding_box_factory($bb_west, $bb_north, $bb_east, $bb_south);
	}

	private function createBoundingBoxFromCenter($center, $within_km = 0)
	{
		// if a $center point and $within_km distance was given,
		// create a bounding box that matches those conditions.
		$center_point = explode(',', $center);
		$center_lat = $center_point[0];
		$center_lon = $center_point[1];

		$bounding_box_factory = $this->bounding_box_factory;
		$bounding_box = $bounding_box_factory(
			$center_lon, $center_lat, $center_lon, $center_lat
		);

		if ($within_km) {
			$bounding_box->expandByKilometers($within_km);
		}

		return $bounding_box;
	}

	/**
	 * Get a subquery to return post_point entries within a bounding box
	 * @param  string $bbox Bounding box
	 * @return Database_Query
	 */
	private function getBoundingBoxSubquery(Util_BoundingBox $bounding_box)
	{
		return DB::select('post_id')
			->from('post_point')
			->where(
				DB::expr(
					'CONTAINS(GeomFromText(:bounds), value)',
					[':bounds' => $bounding_box->toWKT()]
				),
				'=',
				1
			);
	}

	/**
	 * Get tags for a post
	 * @param  int   $id  post id
	 * @return array      tag ids for post
	 */
	private function getTagsForPost($id)
	{
		$result = DB::select('tag_id')->from('posts_tags')
			->where('post_id', '=', $id)
			->execute($this->db);
		return $result->as_array(NULL, 'tag_id');
	}


	// UpdatePostRepository
	public function isSlugAvailable($slug)
	{
		return $this->selectCount(compact('slug')) === 0;
	}


	// UpdatePostRepository
	public function doesTranslationExist($locale, $parent_id, $type)
	{
		// If this isn't a translation of an existing post, skip
		if ($type != 'translation')
		{
			return TRUE;
		}

		// Is locale the same as parent?
		$parent = $this->get($parent_id);
		if ($parent->locale === $locale)
		{
			return FALSE;
		}

		// Check for other translations
		return $this->selectCount([
			'type' => 'translation',
			'parent_id' => $parent_id,
			'locale' => $locale
			]) === 0;
	}

	// UpdateRepository
	public function create(Entity $entity)
	{
		$post = $entity->setState(['created' => time()])->asArray();

		// Remove attribute values and tags
		unset($post['values'], $post['tags']);

		// Create the post
		$id = $this->executeInsert($this->removeNullValues($post));

		if ($entity->tags)
		{
			// Update post-tags
			$this->updatePostTags($id, $entity->tags);
		}

		if ($entity->values)
		{
			// Update post-values
			$this->updatePostValues($id, $entity->values);
		}

		return $id;
	}

	// UpdateRepository
	public function update(Entity $entity)
	{
		$post = $entity->setState(['updated' => time()])->getChanged();

		// Remove attribute values and tags
		unset($post['values'], $post['tags']);

		// Update the post
		$count = $this->executeUpdate(['id' => $entity->id], $post);

		if ($entity->tags)
		{
			// Update post-tags
			$this->updatePostTags($entity->id, $entity->tags);
		}

		if ($entity->values)
		{
			// Update post-values
			$this->updatePostValues($entity->id, $entity->values);
		}

		// @todo Save revision
		//$this->createRevision($id);

		return $count;
	}

	protected function updatePostValues($post_id, $attributes)
	{
		$this->post_value_factory->proxy()->deleteAllForPost($post_id);

		foreach ($attributes as $key => $values)
		{
			$attribute = $this->form_attribute_repo->getByKey($key);
			$repo = $this->post_value_factory->getRepo($attribute->type);

			foreach ($values as $val)
			{
				$repo->createValue($val, $attribute->id, $post_id);
			}
		}
	}

	protected function updatePostTags($post_id, $tags)
	{
		// Load existing tags
		$existing = $this->getTagsForPost($post_id);

		$insert = DB::insert('posts_tags', ['post_id', 'tag_id']);

		$tag_ids = [];
		$new_tags = FALSE;
		foreach ($tags as $tag)
		{
			// Find the tag by id or name
			// @todo this should happen before we even get here
			$tag_entity = $this->tag_repo->getByTag($tag);
			if (! $tag_entity->id)
			{
				$tag_entity = $this->tag_repo->get($tag);
			}

			// Does the post already have this tag?
			if (! in_array($tag_entity->id, $existing))
			{
				// Add to insert query
				$insert->values([$post_id, $tag_entity->id]);
				$new_tags = TRUE;
			}

			$tag_ids[] = $tag_entity->id;
		}

		// Save
		if ($new_tags)
		{
			$insert->execute($this->db);
		}

		// Remove any other tags
		if (! empty($tag_ids))
		{
			DB::delete('posts_tags')
				->where('tag_id', 'NOT IN', $tag_ids)
				->execute($this->db);
		}
	}
}
