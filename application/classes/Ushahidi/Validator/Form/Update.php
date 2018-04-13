<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\FormRepository;
use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Form_Update extends Validator
{
	protected $default_error_source = 'form';
	protected $repo;

	/**
	 * Construct
	 *
	 * @param FormRepository  $repo
	 */
	public function __construct(FormRepository $repo)
	{
		$this->repo = $repo;
	}


	protected function getRules()
	{
		// Always check validation for name
		$name = $this->validation_engine->getFullData('name');
		if ($name) {
			$data = $this->validation_engine->getData();
			$data['name'] = $name;
			$this->validation_engine->setData($data);
		}
		// End

		return [
			'name' => [
				['not_empty'],
				['min_length', [':value', 2]],
				['regex', [':value', Validator::REGEX_STANDARD_TEXT]], // alpha, number, punctuation, space
			],
			'description' => [
				['is_string'],
			],
			'color' => [
				['color'],
			],
			'disabled' => [
				['in_array', [':value', [true, false]]]
			],
		];
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
