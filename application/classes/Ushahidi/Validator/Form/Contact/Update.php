<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Contact Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Form_Contact_Update extends Validator
{
	protected $default_error_source = 'form_contact';
	protected $form_repo;
	protected $contact_repo;
	protected $form_contact_repo;
	protected $phone_validator;

	public function setPhoneValidator($validator) {
		$this->phone_validator = $validator;

	}
	public function setFormRepo(\Ushahidi\Core\Entity\FormRepository $form_repo)
	{
		$this->form_repo = $form_repo;
	}

	public function setFormContactRepo(\Ushahidi\Core\Entity\FormContactRepository $form_contact_repo)
	{
		$this->form_contact_repo = $form_contact_repo;
	}

	public function setContactRepo(\Ushahidi\Core\Entity\ContactRepository $contact_repo)
	{
		$this->contact_repo = $contact_repo;
	}

	protected function getRules()
	{
		return [
			'form_id' => [
				['digit'],
				[[$this->form_repo, 'exists'], [':value']],
			],
			'country_code' => [
				['not_empty'],
			],
		];
	}
	public function contactIsValid($value, $fullData) {
		try {
			$number = $this->phone_validator->parse($value, $fullData['country_code']);
			$isValid = $this->phone_validator->isValidNumber($number);
			return $isValid;
		} catch (\libphonenumber\NumberParseException $e) {
			return false;
		}
	}

}
