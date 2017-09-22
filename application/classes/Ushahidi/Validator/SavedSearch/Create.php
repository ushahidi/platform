<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Set Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
use Ushahidi\Core\Traits\UserContext;

class Ushahidi_Validator_SavedSearch_Create extends Ushahidi_Validator_SavedSearch_Update
{
	use UserContext;
	protected function getRules()
	{
		return array_merge_recursive(parent::getRules(), [
	    'name' => [
			['not_empty'],
		],
        'user_id' => [
            [[$this->user_repo, 'exists'], [':value']],
            [[$this, 'isUserSelf'], [':fulldata']],

        ],
		'filter' => [
				['not_empty'],
			]
		]);
	}

}
