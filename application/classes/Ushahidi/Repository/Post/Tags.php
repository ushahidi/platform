<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Relation Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Usecase\Post\UpdatePostTagRepository;

class Ushahidi_Repository_Post_Tags extends Ushahidi_Repository_Post_Value
{
	protected $tag_repo;

	/**
	 * Construct
	 * @param Database              $db
	 * @param TagRepo               $tag_repo
	 */
	public function __construct(
			Database $db,
			UpdatePostTagRepository $tag_repo
		)
	{
		parent::__construct($db);
		$this->tag_repo = $tag_repo;
	}

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'posts_tags';
	}

	// Override selectQuery to fetch attribute 'key' too
	protected function selectQuery(Array $where = [])
	{
		$query = parent::selectQuery($where);

		// Select 'tag_id' as value too
		$query->select(
				['posts_tags.tag_id', 'value']
			);

		return $query;
	}

	// PostValueRepository
	public function getValueQuery($form_attribute_id, array $matches)
	{
		$query = $this->selectQuery(compact('form_attribute_id'))
			->and_where_open();

		foreach ($matches as $match) {
			$query->or_where('tag_id', 'LIKE', "%$match%");
		}

		$query->and_where_close();

		return $query;
	}

	// UpdatePostValueRepository
	public function createValue($value, $form_attribute_id, $post_id)
	{
		$tag_id = $this->parseTag($value);
		$input = compact('tag_id', 'form_attribute_id', 'post_id');
		$input['created'] = time();

		return $this->executeInsert($input);
	}

	// UpdatePostValueRepository
	public function updateValue($id, $value)
	{
		$tag_id = $this->parseTag($value);
		$update = compact($tag_id);
		if ($id && $update)
		{
			$this->executeUpdate(compact('id'), $update);
		}
	}

	protected function parseTag($tag)
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

		return $tag_entity->id;
	}

}
