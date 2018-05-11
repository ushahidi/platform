<?php

/**
 * Ushahidi Form Contact Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\Form\Contact;

use Ushahidi\Core\Tool\Validator;

class Update extends Validator
{
    protected $default_error_source = 'form_contact';
    protected $form_repo;
    protected $contact_repo;
    protected $form_contact_repo;

    public function setFormContactRepo(\Ushahidi\Core\Entity\FormContactRepository $form_contact_repo)
    {
        $this->form_contact_repo = $form_contact_repo;
    }

    public function setFormRepo(\Ushahidi\Core\Entity\FormRepository $form_repo)
    {
        $this->form_repo = $form_repo;
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
}
