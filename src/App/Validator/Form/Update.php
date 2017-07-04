<?php

/**
 * Ushahidi Form Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\Form;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\FormRepository;
use Ushahidi\Core\Tool\Validator;

class Update extends Validator
{
	protected $default_error_source = 'form';
	protected $repo;
	protected $limits;

	/**
	 * Construct
	 *
	 * @param FormRepository  $repo
	 */
	public function __construct(FormRepository $repo, array $limits)
	{
		$this->repo = $repo;
		$this->limits = $limits;
	}


	protected function getRules()
	{
		return [
			'name' => [
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

    public function checkPostTypeLimit(\Kohana\Validation\Validation $validation)
    {
		if ($this->limits['forms'] !== true) {
			$total_forms = $this->repo->getTotalCount();

			if ($total_forms >= $this->limits['forms']) {
				$validation->error('name', 'postTypeLimitReached');
			}
        }
    }
}
