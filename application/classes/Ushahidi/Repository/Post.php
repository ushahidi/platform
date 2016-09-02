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
use Ushahidi\Core\Entity\FormStageRepository;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Entity\PostValueContainer;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\PostSearchData;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Usecase\Post\StatsPostRepository;
use Ushahidi\Core\Usecase\Post\UpdatePostRepository;
use Ushahidi\Core\Usecase\Post\UpdatePostTagRepository;
use Ushahidi\Core\Usecase\Set\SetPostRepository;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\Permissions\ManagePosts;
use Ushahidi\Core\Traits\PermissionAccess;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Tool\Permissions\Permissionable;

use Aura\DI\InstanceFactory;

class Ushahidi_Repository_Post extends Ushahidi_Repository implements
	PostRepository,
	UpdatePostRepository,
	SetPostRepository,
	Permissionable
{
	use UserContext;

	// Use the JSON transcoder to encode properties
	use Ushahidi_JsonTranscodeRepository;

	// Provides `getPermission`
	use ManagePosts;

	// Provides `hasPermission`
	use PermissionAccess;

	// Checks if user is Admin
	use AdminAccess;

	protected $form_attribute_repo;
	protected $form_stage_repo;
	protected $post_value_factory;
	protected $bounding_box_factory;
	protected $tag_repo;

	protected $include_value_types = [];
	protected $include_attributes = [];

	/**
	 * Construct
	 * @param Database                              $db
	 * @param FormAttributeRepository               $form_attribute_repo
	 * @param FormStageRepository                   $form_stage_repo
	 * @param Ushahidi_Repository_Post_ValueFactory  $post_value_factory
	 * @param Aura\DI\InstanceFactory               $bounding_box_factory
	 */
	public function __construct(
			Database $db,
			FormAttributeRepository $form_attribute_repo,
			FormStageRepository $form_stage_repo,
			Ushahidi_Repository_Post_ValueFactory $post_value_factory,
			InstanceFactory $bounding_box_factory,
			UpdatePostTagRepository $tag_repo
		)
	{
		parent::__construct($db);

		$this->form_attribute_repo = $form_attribute_repo;
		$this->form_stage_repo = $form_stage_repo;
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
				'sets' => $this->getSetsForPost($data['id']),
				'completed_stages' => $this->getCompletedStagesForPost($data['id']),
			];
		}

		return new Post($data);
	}

	// Ushahidi_JsonTranscodeRepository
	protected function getJsonProperties()
	{
		return ['published_to'];
	}

	// Override selectQuery to fetch 'value' from db as text
	protected function selectQuery(Array $where = [])
	{
		$query = parent::selectQuery($where);

		// Join to messages and load message id
		$query->join('messages', 'LEFT')->on('posts.id', '=', 'messages.post_id')
			->select(['messages.id', 'message_id'], ['messages.type', 'source'], ['messages.contact_id', 'contact_id']);

		// Join to form
		$query->join('forms', 'LEFT')->on('posts.form_id', '=', 'forms.id')
			->select(['forms.color', 'color']);

		return $query;
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

	protected function getCompletedStagesForPost($id)
	{
		$result = DB::select('form_stage_id', 'completed')
			->from('form_stages_posts')
			->where('post_id', '=', $id)
			->where('completed', '=', 1)
			->execute($this->db);

		return $result->as_array(NULL, 'form_stage_id');
	}

	// Ushahidi_Repository
	public function getSearchFields()
	{
		return [
			'status', 'type', 'locale', 'slug', 'user',
			'parent', 'form', 'set', 'q', /* LIKE title, content */
			'created_before', 'created_after',
			'updated_before', 'updated_after',
			'bbox', 'tags', 'values', 'current_stage',
			'center_point', 'within_km',
			'published_to',
			'include_types', 'include_attributes', // Specify values to include
			'group_by', 'group_by_tags', 'group_by_attribute_key', // Group results
			'timeline', 'timeline_interval', 'timeline_attribute' // Timeline params
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
		$table = $this->getTable();

		$status = $search->getFilter('status', 'published');
		if ($status !== 'all')
		{
			$query->where("$table.status", '=', $status);
		}

		foreach (['type', 'locale', 'slug'] as $key)
		{
			if ($search->$key)
			{
				$query->where("$table.$key", '=', $search->$key);
			}
		}

		// If user = me, replace with current user id
		if ($search->user === 'me')
		{
			$search->user = $this->getUserId();
		}

		foreach (['user', 'parent', 'form'] as $key)
		{
			if (!empty($search->$key))
			{
				// Make sure we have an array
				if (!is_array($search->$key)) {
					$search->$key = explode(',', $search->$key);
				}

				// Special case: 'none' looks for null
				if (in_array('none', $search->$key)) {
					$query->and_where_open()
						->where("$table.{$key}_id", 'IS', NULL)
						->or_where("$table.{$key}_id", 'IN', $search->$key)
						->and_where_close();
				} else {
					$query->where("$table.{$key}_id", 'IN', $search->$key);
				}
			}
		}

		if ($search->q)
		{
			// search terms are all wrapped as a series of OR conditions
			$query->and_where_open();

			// searching in title / content
			$query
				->where("$table.title", 'LIKE', "%$search->q%")
				->or_where("$table.content", 'LIKE', "%$search->q%");

			if (is_numeric($search->q)) {
				// if `q` is numeric, could be searching for a specific id
				$query->or_where('id', '=', $search->q);
			}

			$query->and_where_close();
		}


		if ($search->id)
		{
			//searching for specific post id, used for single post in set searches
			$query->where('id', '=', $search->id);
		}

		// date chcks
		if ($search->created_after)
		{
			$created_after = strtotime($search->created_after);
			$query->where("$table.created", '>=', $created_after);
		}

		if ($search->created_before)
		{
			$created_before = strtotime($search->created_before);
			$query->where("$table.created", '<=', $created_before);
		}

		if ($search->updated_after)
		{
			$updated_after = strtotime($search->updated_after);
			$query->where("$table.updated", '>=', $updated_after);
		}

		if ($search->updated_before)
		{
			$updated_before = strtotime($search->updated_before);
			$query->where("$table.updated", '<=', $updated_before);
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

		// Published to
		if ($search->published_to) {
			$query
				->where("$table.published_to", 'LIKE', "%'$search->published_to'%")
				;
		}

		if ($search->current_stage) {
			$stages = $search->current_stage;
			if (!is_array($stages)) {
				$stages = explode(',', $stages);
			}

			/**
			 * Here be dragons
			 * The purpose of this query is to return the set of posts which
			 * have current stage X. In this case, current stage X is actually the
			 * the stage the post has NOT yet completed - which is the stage with.
			 * the lowest priority.
			 * For example:
			 * If I have 3 stages for a given Post Type, I am on stage 1 if
			 * I have completed no stages and I am on stage 3 if I have completed
			 * stages 1 and 2.
			 * If I have completed stages 1 and 3, I am on stage 2 as stages are considered
			 * to be sequential
			 */

			/**
			 * This query is responsible for returning all the
			 * stages for a given post id, where the stage has been completed.
			 * This is used to check which stages the Post has not yet completed
			 */

			$stages_posts = DB::select('form_stage_id')
				->from('form_stages_posts')
				->where('post_id', '=', DB::expr('posts.id'))
				->and_where('completed', '=', '1');

			/**
			 * This query returns the IDs for the stages that we are filtering by
			 */
			$forms_sub = DB::select('form_stages.form_id')
				->from('form_stages')
				->where('form_stages.id', 'IN', $stages);

			/**
			 * This is the master query, it collects all the posts
			 * missing stages that we are filtering by.
			 */
			$sub = DB::select(array('posts.id','p_id'), array('form_stages.id', 'fs_id'), 'priority', 'posts.form_id', 'forms.id')
				->from('posts')
				->join('forms')
				// Here we join to the forms table based on the set of stage ids we are filtering by
				->on('posts.form_id', '=', 'forms.id')
				->on('forms.id', 'IN', $forms_sub)
				->join('form_stages')
				// Here we join to the form_stages table based on the form id
				// and a check that the current post has not already completed this stage
				->on('form_stages.form_id', 'IN', $forms_sub)
				->on('form_stages.id', 'NOT IN', $stages_posts)
		// We group the results by post id
		->group_by('p_id')
		// We reduce the list to ensure that only results missing the stages to filter by are returned
		->having('form_stages.id', 'IN', $stages)
				// Finally we order the results by priority to ensure that if, for example,
				// a post is missing multiple stages we only consider the first uncompleted stage
				->order_by('priority');

			//This step wraps the query and returns only the posts ids without the extra data such as form, stage or priority
	  $posts_sub = DB::select('p_id')
		  ->from(array($sub, 'sub'));

			$query
				->where('posts.id', 'IN', $posts_sub);
		}

		// Filter by tag
		if (!empty($search->tags))
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
		if (!empty($search->set))
		{
			$set = $search->set;
			if (!is_array($set)) {
				$set = explode(',', $set);
			}

			$query
				->join('posts_sets', 'INNER')->on('posts.id', '=', 'posts_sets.post_id')
				->where('posts_sets.set_id', 'IN', $set);
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

		$user = $this->getUser();
		// If there's no logged in user, or the user isn't admin
		// restrict our search to make sure we still return SOME results
		// they are allowed to see
		if (!$user->id) {
			$query->where("$table.status", '=', 'published');
		} elseif (!$this->isUserAdmin($user) and
				  !$this->hasPermission($user, $this->getPermission())) {
			$query
				->and_where_open()
				->where("$table.status", '=', 'published')
				->or_where("$table.user_id", '=', $user->id)
				->and_where_close();
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
	public function getPublishedTotal()
	{
		return (int) $this->selectCount(['posts.status' => 'published']);
	}

	// StatsPostRepository
	public function getGroupedTotals(SearchData $search)
	{
		// Create a new query to select posts count
		$this->search_query = DB::select([DB::expr('COUNT(DISTINCT posts.id)'), 'total'])
			->from($this->getTable());

		// Quick hack to ensure all posts are available to
		// group_by=status
		if ($search->group_by === 'status' && ! $search->status) {
			$search->status = 'all';
		}

		// Set filters
		// Note: we're calling setSearchConditions, not setSearchParams
		// because we don't want to set sorting params
		$this->setSearchConditions($search);

		// Group by time-intervals
		if ($search->timeline)
		{
			// Default to posts created
			$time_field = 'posts.created';

			if ($search->timeline_attribute === 'created' || $search->timeline_attribute == 'updated')
			{
				// Assumed created / updated means the builtin posts created/updated times
				$time_field = 'posts.' . $search->timeline_attribute;
			}
			elseif ($search->timeline_attribute)
			{
				// Find the attribute
				$key = $search->timeline_attribute;
				$attribute = $this->form_attribute_repo->getByKey($key);
				if ($attribute)
				{
					// Get the post_TYPE table.
					$sub = $this->post_value_factory
						->getRepo($attribute->type)
						->getValueTable();

					// Join to attribute
					$this->search_query
						->join([$sub, 'Time_'.ucfirst($key)], 'INNER')
							->on('form_attribute_id', '=', DB::expr($attribute->id))
							->on('posts.id', '=', 'Time_'.ucfirst($key).'.post_id');

					// Use the attribute `value` as our time
					$time_field = 'Time_'.ucfirst($key).'.value';
				}
			}

			$this->search_query
				->select([
					DB::expr('FLOOR('.$time_field.'/:interval)*:interval', [':interval' => (int)$search->getFilter('timeline_interval', 86400)]),
					'time_label'
				])
				->group_by('time_label');
		}

		// Group by attribute
		if ($search->group_by === 'attribute' AND $search->group_by_attribute_key)
		{
			$key = $search->group_by_attribute_key;
			$attribute = $this->form_attribute_repo->getByKey($key);

			if ($attribute)
			{
				$sub = $this->post_value_factory
					->getRepo($attribute->type)
					->getValueTable();

				$this->search_query
					->join([$sub, 'Group_'.ucfirst($key)], 'INNER')
						->on('form_attribute_id', '=', DB::expr($attribute->id))
						->on('posts.id', '=', 'Group_'.ucfirst($key).'.post_id')
					->select(['Group_'.ucfirst($key).'.value', 'label'])
					->group_by('label');
			}
		}
		// Group by status
		elseif ($search->group_by === 'status')
		{
			$this->search_query
				->select(['posts.status', 'label'])
				->group_by('label');
		}
		// Group by form
		elseif ($search->group_by === 'form')
		{
			$this->search_query
				->join('forms', 'LEFT')->on('posts.form_id', '=', 'forms.id')
				->select(['forms.name', 'label'])
				->select(['forms.id', 'id'])
				->group_by('posts.form_id');
		}
		// Group by tags
		elseif ($search->group_by === 'tags')
		{
			/**
			 * The output query looks something like
			 * SELECT
			 * `parents`.`tag` AS `label`,
			 * COUNT(DISTINCT posts.id) AS `total`
			 * FROM `posts`
			 * JOIN `posts_tags` ON (`posts`.`id` = `posts_tags`.`post_id`)
			 * JOIN `tags` ON (`posts_tags`.`tag_id` = `tags`.`id`)
			 * JOIN `tags` as `parents` ON (`parents`.`id` = `tags`.`parent_id` OR `parents`.`id` = `posts_tags`.`tag_id`)
			 * WHERE `status` = 'published' AND `posts`.`type` = 'report'
			 * AND `parents`.`parent_id` IS NULL
			 * GROUP BY `parents`.`id`
			 */

			// Count by tag but also include child counts in the parent count
			$this->search_query
				->join('posts_tags')->on('posts.id', '=', 'posts_tags.post_id')
				->join('tags')->on('posts_tags.tag_id', '=', 'tags.id')
				->join(['tags', 'parents'])
					// Slight hack to avoid kohana db forcing multiple ON clauses to use AND not OR.
					->on(DB::expr("`parents`.`id` = `tags`.`parent_id` OR `parents`.`id` = `posts_tags`.`tag_id`"), '', DB::expr(""))
				->select(['parents.tag', 'label'])
				->select(['parents.id', 'id'])
				->group_by('parents.id');

			// Limit tags to a top level, or a specific parent.
			if ($search->group_by_tags !== 'all') {
				if ($search->group_by_tags) {
					$this->search_query
						->where('parents.parent_id', '=', $search->getFilter('group_by_tags', null));
				} else {
					// Special case: top level categories could have parent_id NULL or 0
					// @todo try to ensure parent_id is always NULL and migrate 0 -> NULL
					$this->search_query
						->and_where_open()
						->where('parents.parent_id', 'IS', NULL)
						->or_where('parents.parent_id', '=', 0)
						->and_where_close();
				}
			}
		}
		// If no group_by just count all posts
		else {
			$this->search_query
				->select([DB::expr('"all"'), 'label']);
		}

		// .. Add orderby time *after* order by groups
		if ($search->timeline)
		{
			// Order by label, then time
			$this->search_query->order_by('label');
			$this->search_query->order_by('time_label');
		}
		else {
			// Order by count, then label
			$this->search_query->order_by('total', 'DESC');
			$this->search_query->order_by('label');
		}

		// Fetch the results and...
		$results = $this->search_query->execute($this->db);

		// ... return them as an array
		return $results->as_array();
	}

	// PostRepository
	public function getByIdAndParent($id, $parent_id, $type)
	{
		return $this->getEntity($this->selectOne([
			'posts.id' => $id,
			'posts.parent_id' => $parent_id,
			'posts.type' => $type
		]));
	}

	// PostRepository
	public function getByLocale($locale, $parent_id, $type)
	{
		return $this->getEntity($this->selectOne([
			'posts.locale' => $locale,
			'posts.parent_id' => $parent_id,
			'posts.type' => $type
		]));
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
			'posts.type' => 'translation',
			'posts.parent_id' => $parent_id,
			'posts.locale' => $locale
			]) === 0;
	}

	// UpdateRepository
	public function create(Entity $entity)
	{

		$post = $entity->asArray();
		$post['created'] = time();

		// Remove attribute values and tags
		unset($post['values'], $post['tags'], $post['completed_stages'], $post['sets'], $post['source'], $post['color']);

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

		if ($entity->completed_stages)
		{
			// Update post-stages
			$this->updatePostStages($id, $entity->form_id, $entity->completed_stages);
		}

		return $id;
	}

	// UpdateRepository
	public function update(Entity $entity)
	{
		$post = $entity->getChanged();
		$post['updated'] = time();

		// Remove attribute values and tags
		unset($post['values'], $post['tags'], $post['completed_stages'], $post['sets'], $post['source'], $post['color']);

		// Update the post
		$count = $this->executeUpdate(['id' => $entity->id], $post);

		if ($entity->hasChanged('tags'))
		{
			// Update post-tags
			$this->updatePostTags($entity->id, $entity->tags);
		}

		if ($entity->hasChanged('values'))
		{
			// Update post-values
			$this->updatePostValues($entity->id, $entity->values);
		}

		if ($entity->hasChanged('completed_stages'))
		{
			// Update post-stages
			$this->updatePostStages($entity->id, $entity->form_id, $entity->completed_stages);
		}

		return $count;
	}

	protected function updatePostValues($post_id, $attributes)
	{
		$this->post_value_factory->proxy()->deleteAllForPost($post_id);

		foreach ($attributes as $key => $values)
		{
			$attribute = $this->form_attribute_repo->getByKey($key);
			if (!$attribute->id) {
				continue;
			}

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
			if (is_array($tag)) {
				$tag = $tag['id'];
			}

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
				->where('post_id', '=', '$post_id')
				->execute($this->db);
		}
	}


	protected function updatePostStages($post_id, $form_id, $completed_stages)
	{
		if (! is_array($completed_stages))
		{
			$completed_stages = [];
		}

		// Remove any existing entries
		DB::delete('form_stages_posts')
			->where('post_id', '=', $post_id)
			->execute($this->db);

		$insert = DB::insert('form_stages_posts', ['form_stage_id', 'post_id', 'completed']);
		// Get all stages for form
		$form_stages = $this->form_stage_repo->getByForm($form_id);
		foreach ($form_stages as $stage)
		{
			$insert->values([
				$stage->id,
				$post_id,
				in_array($stage->id, $completed_stages) ? 1 : 0
			]);
		}
		// Execute the insert
		$insert->execute($this->db);
	}

	// SetPostRepository
	public function getPostInSet($post_id, $set_id)
	{
		$result = $this->selectQuery(['posts.id' => $post_id])
			->select('posts.*')
			->join('posts_sets', 'INNER')->on('posts.id', '=', 'posts_sets.post_id')
			->where('posts_sets.set_id', '=', $set_id)
			->limit(1)
			->execute($this->db)
			->current();

		return $this->getEntity($result);
	}
}
