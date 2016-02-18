<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi CSV Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Entity\FormRepository;

class Ushahidi_Validator_CSV_Create extends Validator
{
	protected $form_repo;
	protected $max_bytes = 0;
	protected $default_error_source = 'csv';

	public function __construct(FormRepository $form_repo)
	{
		$this->form_repo = $form_repo;
	}

	public function setMaxBytes($max_bytes)
	{
		$this->max_bytes = $max_bytes;
	}
	
	protected function getRules()
	{
		return [
			'form_id' => [
				['numeric'],
				[[$this->form_repo, 'exists'], [':value']],
			],
			'mime' => [
				['not_empty'],
				[[$this, 'validateMime'], [':validation', ':value']],
			],
			'filename' => [
				['not_empty']
			],
			'size' => [
				['not_empty'],
				['range', [':value', 0, $this->max_bytes]],
			],
		];
	}

	public function validateMime($validation, $mime)
	{
		$allowed_mime_types = [
			'text/csv', 'text/plain'
		];

		if (!in_array($mime, $allowed_mime_types)) {
			$validation->error('mime', 'mime_type');
		}
	}
}
