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

class Ushahidi_Validator_CSV_Update extends Validator
{
	protected $form_repo;
	protected $default_error_source = 'csv';

	public function __construct(FormRepository $form_repo)
	{
		$this->form_repo = $form_repo;
	}
	
	protected function getRules()
	{
		return [
			'form_id' => [
				['numeric'],
				[[$this->form_repo, 'exists'], [':value']],
			],
		];
	}
}
