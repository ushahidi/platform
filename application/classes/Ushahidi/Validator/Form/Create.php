<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Ushahidi_Validator_Form_Create extends Ushahidi_Validator_Form_Update
{
	protected function getRules()
	{
		return array_merge_recursive(parent::getRules(), [
			'name' => [['not_empty']],
			[[$this, 'checkPostTypeLimit'], [':validation']],
		]);
	}

	public function checkPostTypeLimit (Validation $validation)
	{
		$config = \Kohana::$config->load('features.limits');

		if ($config['forms'] !== TRUE) {

			$total_forms = $this->repo->getTotalCount();

			if ($total_forms >= $config['forms']) {
				$validation->error('name', 'postTypeLimitReached');
			}
		}
	}
}
