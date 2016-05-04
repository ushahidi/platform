<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Media Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity\MediaRepository;

class Ushahidi_Validator_Post_Media extends Ushahidi_Validator_Post_ValueValidator
{
	protected $media_repo;

	public function __construct(MediaRepository $media_repo)
	{
		$this->repo = $media_repo;
	}

	protected function validate($value)
	{
		if (!Valid::digit($value)) {
			return 'digit';
		}

		if (! $this->repo->exists($value)) {
			return 'exists';
		}
	}
}
