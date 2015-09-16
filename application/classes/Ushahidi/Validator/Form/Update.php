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

	/**
	 * Construct
	 *
	 * @param FormRepository  $form_repo
	 */
	public function __construct(
		FormRepository $form_repo
    )
	{
		$this->form_repo = $form_repo;
	}


	protected function getRules()
	{
		return [
			'name' => [
				['min_length', [':value', 2]],
				['regex', [':value', Validator::REGEX_STANDARD_TEXT]], // alpha, number, punctuation, space
				[[$this, 'checkPostTypeLimit'], [':validation']],
			],
			'description' => [
				['is_string'],
			],
			'disabled' => [
				['in_array', [':value', [true, false]]]
			],
		];
	}

  public function checkPostTypeLimit (Validation $validation)
  {
    $config = \Kohana::$config->load('features.client-limits');

    if ($config['num_post_types'] > 1) {
  
      $total_post_types = $this->form_repo->countEntities(); 

      if ($total_post_types >= $config['num_post_types']) {
        $validation->error('name', 'postTypeLimitReached');
      }
    }
  }
}
