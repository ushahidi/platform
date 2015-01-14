<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi FormAttribute Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;

use Ushahidi\Core\Tool\Validator;


class Ushahidi_Validator_Form_Attribute_Create extends Ushahidi_Validator_Form_Attribute_Update
{
    public function check(Data $input)
    {
        $this->valid = Validation::factory($input->asArray());

        // same rules as update, plus several fields cannot be empty
        $this->attachRules(array_merge_recursive($this->getRules(),
            array_fill_keys([
                'key', 'label', 'input', 'type', 'cardinality', 'form_group_id',
            ], [['not_empty']])
        ));

        return $this->valid->check();
    }
}
