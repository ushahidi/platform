<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Group Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\FormRepository;
use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Form_Group_Update implements Validator
{
	protected $form_repo;

	protected $valid;

	public function __construct(FormRepository $form_repo)
	{
		$this->form_repo = $form_repo;
	}

	public function check(Entity $entity)
	{
		$this->valid = Validation::factory($entity->getChanged())
			->rules('form_id', [
				['digit'],
				[[$this->form_repo, 'doesFormExist'], [':value']],
			])
			->rules('label', [
				['min_length', [':value', 2]],
				['regex', [':value', Validator::REGEX_STANDARD_TEXT]], // alpha, number, punctuation, space
			])
			->rules('priority', [
				['digit'],
			])
			->rules('icon', [
				['alpha'],
			]);

		return $this->valid->check();
	}

	public function errors($from = 'form')
	{
		return $this->valid->errors($from);
	}
}
