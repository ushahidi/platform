<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Media Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity\TagRepository;

class Ushahidi_Validator_Post_Tags extends Ushahidi_Validator_Post_ValueValidator
{
	protected $repo;

	public function __construct(TagRepository $tags_repo)
	{
		$this->repo = $tags_repo;
	}

	protected function validate($value)
	{
		if (is_array($value)) {
			$value = $value['id'];
		}

		if (!$this->repo->doesTagExist($value)) {
			return 'tagExists';
		}
	}
}
