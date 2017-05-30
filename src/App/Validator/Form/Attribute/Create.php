<?php

/**
 * Ushahidi FormAttribute Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\Form\Attribute;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Validator;

class Create extends Update
{
    public function getRules()
    {
        return array_merge_recursive(parent::getRules(), [
            'label' => [
                ['not_empty'],
            ],
            'form_stage_id' => [
                ['not_empty'],
            ],
        ]);
    }
}
