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

class Create extends Update
{
    protected $default_error_source = 'form_stage';

    protected function getRules()
    {
        return [
            'form_id' => [
                ['not_empty'],
            ],
            'label' => [
                ['not_empty'],
            ],
            'type' => [
                ['in_array', [':value', [
                    'post',
                    'task'
                ]]],
            ],
        ];
    }
}
