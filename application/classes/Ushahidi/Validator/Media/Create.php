<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Media Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Data;
use Ushahidi\Tool\Validator;

class Ushahidi_Validator_Media_Create implements Validator
{
	protected $valid;

	public function check(Data $input)
	{
		$max_file_size = Kohana::$config->load('media.max_file_upload_size');
		$allowed_file_types = ['gif','jpg','jpeg','png'];

		$this->valid = Validation::factory($input->asArray())
			->rules('file', array(
					array('not_empty'),
					array('Upload::valid'),
					array('Upload::type', array(':value', $allowed_file_types)),
					array('Upload::size', array(':value', $max_file_size)),
				))
			->rules('user_id', array(
					array('digit'),
				))
			->rules('caption', array(
					// alphas, numbers, punctuation, and spaces
					array('regex', array(':value', '/^[\pL\pN\pP ]++$/uD')),
				));

		return $this->valid->check();
	}

	public function errors($from = 'media')
	{
		return $this->valid->errors($from);
	}
}
