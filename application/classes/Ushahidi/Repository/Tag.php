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

class Ushahidi_Repository_Tag extends Ushahidi_Repository_Collection implements TagRepository
{
	// Ushahidi_Repository_Collection
	protected function getTable()
	{
		return 'tags';
	}

	// Ushahidi_Repository_Collection
	protected function getEntity(Array $data = NULL)
	{
		return new Tag($data);
	}

	// TagRepository
	public function get($id)
	{
		$tags = $this->read($this->getEntity(compact('id')));
		// if no results, return an empty tag
		return $tags ? current($tags) : $this->getEntity();
	}

	// TagRepository
	public function getAllByParent($parent_id)
	{
		return $this->read($this->getEntity(compact('parent_id')));
	}
}

