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

class Ushahidi_Validator_Media_Create implements Validator
{
	protected $valid;

	public function check(Entity $entity)
	{
		// Not entirely thrilled with these changes, as it defers validation of the
		// upload to **after** the file has already been created. Not sure how to get
		// around it though, ends up being a chicken and egg problem. @fixme
		$max_file_size = Num::bytes(Kohana::$config->load('media.max_file_upload_size'));
		$allowed_types = array_map(['File', 'mime_by_ext'], ['gif','jpg','jpeg','png']);

		$this->valid = Validation::factory($entity->asArray())
			->rules('user_id', [
				['digit'],
			])
			->rules('caption', [
				// alphas, numbers, punctuation, and spaces
				['regex', [':value', '/^[\pL\pN\pP ]++$/uD']],
			])
			->rules('mime', [
				['not_empty'],
				['in_array', [':value', $allowed_types]]
			])
			->rules('o_size', [
				['not_empty'],
				['range', [':value', 1, $max_file_size]]
			]);

		return $this->valid->check();
	}

	public function errors($from = 'media')
	{
		return $this->valid->errors($from);
	}
}
