<?php

/**
 * Ushahidi Form Contact Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Validator\Form\Contact;

use Ushahidi\Modules\V3\Validator\LegacyValidator;
use Ushahidi\Core\Entity\FormRepository;

class Update extends LegacyValidator
{
    protected $default_error_source = 'form_contact';
    protected $form_repo;

    public function setFormRepo(FormRepository $form_repo)
    {
        $this->form_repo = $form_repo;
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
