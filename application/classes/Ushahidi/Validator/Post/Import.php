<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Create Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */


class Ushahidi_Validator_Post_Import extends Ushahidi_Validator_Post_Create
{
  protected function getRules()
	{
    // We remove the rules validating required stages
    // as stages are not validated during an import
		return array_merge(parent::getRules(), [
      'values' => [
				[[$this, 'checkValues'], [':validation', ':value', ':fulldata']]
		  ],
      'completed_stages' => [
				[[$this, 'checkStageInForm'], [':validation', ':value', ':fulldata']]
      ]
  ]);
	}
}
