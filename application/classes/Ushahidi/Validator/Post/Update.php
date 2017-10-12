<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Post_Update extends Ushahidi_Validator_Post_Create
{
	protected function getRules()
	{
		return array_merge_recursive(parent::getRules(), [
            // There is no entity that is guaranteed to be present for Lock
            // We instead piggy back on the Title field which is required
            // To ensure that if the Post is currently Locked by a user other than the
            // current user we reject the Post at the validation stage
			'title' => [
				[[$this, 'checkLock'], [':validation', ':fulldata']],
			],
		]);
	}

    /**
	 * Check that there is no lock held by a different user for this Post
	 *
	 * @param  Validation $validation
	 * @param  Array      $attributes
	 * @param  Array      $fullData
	 */
	public function checkLock(Validation $validation, $fullData)
	{
        // Check if Post is locked
        if ($this->post_lock_repo->postIsLocked($fullData['id']))
        {
            $validation->error('title', 'alreadyLockedByDifferentUser');
            return;
        }
	}
}
