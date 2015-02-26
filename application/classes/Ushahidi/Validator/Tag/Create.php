<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Tag Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Tag_Create extends Ushahidi_Validator_Tag_Update
{
	protected function getRules()
	{
		return array_merge_recursive(parent::getRules(), [
			'tag' => [
				['not_empty'],
			],
			'slug' => [
				['not_empty'],
			],
			'type' => [
				['not_empty'],
			],
		]);
	}
}
