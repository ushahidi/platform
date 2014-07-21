<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Value Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\PostValue;
use Ushahidi\Entity\PostValueRepository;

abstract class Ushahidi_Repository_PostValue extends Ushahidi_Repository implements PostValueRepository
{

	// Ushahidi_Repository
	protected function getEntity(Array $data = null)
	{
		return new PostValue($data);
	}

	// PostValueRepository
	public function get($id)
	{
		return new PostValue($this->selectOne(compact('id')));
	}

	// PostValueRepository
	public function getAllForPost($post_id)
	{
		$query = $this->selectQuery(compact($post_id));

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

}
