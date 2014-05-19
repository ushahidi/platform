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
use Ushahidi\Usecase\Tag\CreateTagRepository;

class Ushahidi_Repository_Tag implements CreateTagRepository
{
	private $created_id;
	private $created_ts;

	// CreateTagRepository
	public function isSlugAvailable($slug)
	{
		$query = DB::select('id')
			->from('tags')
			->where('slug', '=', $slug)
			;

		$results = $query->execute();
		return count($results) === 0;
	}

	// CreateTagRepository
	public function createTag(Array $input)
	{
		$input['created'] = $this->created_ts = time();

		$query = DB::insert('tags')
			->columns(array_keys($input))
			->values(array_values($input))
			;

		list($this->created_id) = $query->execute();
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
}

