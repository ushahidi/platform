<?php

/**
 * Ushahidi Form Stage Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\Form\Stage;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\FormRepository;
use Ushahidi\Core\Tool\Validator;

class Update extends Validator
{
    protected $form_repo;
    protected $default_error_source = 'form';

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
            'label' => [
                ['min_length', [':value', 2]],
                ['regex', [':value', Validator::REGEX_STANDARD_TEXT]], // alpha, number, punctuation, space
            ],
            'priority' => [
                ['digit'],
            ],
            'icon' => [
                ['alpha'],
            ],
        ];
    }
}
