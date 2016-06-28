<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi FormAttribute Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Validator;


class Ushahidi_Validator_Form_Attribute_Create extends Ushahidi_Validator_Form_Attribute_Update
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
