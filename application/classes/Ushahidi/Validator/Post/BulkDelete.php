<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Bulk Update Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Ushahidi_Validator_Post_BulkDelete extends Ushahidi_Validator_Post_BulkUpdate
{
	protected function getRules()
	{

		return [
			'filters' => [
				[[$this, 'checkFilters'], [':validation',':value']],
			],
		];
	}
}
