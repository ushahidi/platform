<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Tag Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\Tag;
use Ushahidi\Entity\TagRepository;
use Ushahidi\Usecase\Tag\CreateTagRepository;

class Ushahidi_Repository_Tag extends Ushahidi_Repository implements
	TagRepository,
	CreateTagRepository
{
	private $created_id;
	private $created_ts;

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'tags';
	}

	// TagRepository
	public function get($id)
	{
		return new Tag($this->selectOne(compact('id')));
	}

	// CreateTagRepository
	public function isSlugAvailable($slug)
	{
		return $this->selectCount(compact('slug')) === 0;
	}

	// CreateTagRepository
	public function createTag($tag, $slug, $description, $type, $color = null, $icon = null, $priority = 0)
	{
		$input = compact('tag', 'slug', 'description', 'type');

		// Add optional fields
		$optional = array_filter(compact('color', 'icon', 'priority'));
		if ($optional) {
			$input += $optional;
		}

		$input['created'] = $this->created_ts = time();

		$this->created_id = $this->insert($input);
	}

	// CreateTagRepository
	public function getCreatedTagId()
	{
		return $this->created_id;
	}

	// CreateTagRepository
	public function getCreatedTagTimestamp()
	{
		return $this->created_ts;
	}

	// CreateTagRepository
	public function getCreatedTag()
	{
		return $this->get($this->created_id);
	}
}

