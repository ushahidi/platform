<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Media Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Media_Update extends Validator
{
	protected function getRules()
	{
		return [
			'user_id' => [
				['digit'],
			],
			'caption' => [
				// alphas, numbers, punctuation, and spaces
				['regex', [':value', '/^[\pL\pN\pP ]++$/uD']],
			],
			'mime' => [
				[[$this, 'validateMime'], [':validation', ':value']],
			],
			'o_size' => [
				[[$this, 'validateSize'], [':validation', ':value']],
			],
			'o_width' => [
				['numeric'],
			],
			'o_height' => [
				['numeric'],
			],
		];
	}
}
