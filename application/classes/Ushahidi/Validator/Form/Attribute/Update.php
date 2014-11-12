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

class Ushahidi_Validator_Form_Attribute_Update implements Validator
{
    protected $valid;
    protected $form_group_repo;

    public function __construct(Ushahidi_Repository_Form_Group $form_group_repo)
    {
        $this->form_group_repo = $form_group_repo;
    }

    public function check(Data $input)
    {
        $this->valid = Validation::factory($input->asArray());
        $this->attachRules($this->getRules());
        return $this->valid->check();
    }

    public function formGroupBelongsToForm($value)
    {
        // don't check against nonexistant data
        if (!$value || !isset($this->valid['form_id'])) {
            return true;
        }

        $group = $this->form_group_repo->get($value);
        return ($group->form_id == $this->valid['form_id']);
    }

    public function errors($from = 'form')
    {
        return $this->valid->errors($from);
    }

    protected function getRules()
    {
        return [
            'key' => [
                ['max_length', [':value', 150]],
                ['alpha_dash', [':value', TRUE]],
            ],
            'label' => [
                ['max_length', [':value', 150]],
            ],
            'input' => [
                ['in_array', [':value', [
                    'text',
                    'textarea',
                    'select',
                    'radio',
                    'checkbox',
                    'checkboxes',
                    'date',
                    'datetime',
                    'location',
                    'number'
                ]]],
            ],
            'type' => [
                ['in_array', [':value', [
                    'decimal',
                    'int',
                    'geometry',
                    'text',
                    'varchar',
                    'point',
                    'datetime',
                    'link'
                ]]],
            ],
            'required' => [
                ['in_array', [':value', [true,false]]],
            ],
            'priority' => [
                ['digit'],
            ],
            'cardinality' => [
                ['digit'],
            ],
            'form_group_id' => [
                ['digit'],
                [[$this->form_group_repo, 'exists'], [':value']],
                [[$this, 'formGroupBelongsToForm'], [':value']],
            ],
            'form_id' => [
                ['digit'],
            ],
        ];
    }

    protected function attachRules($rules = array()) {
        foreach ($rules as $name => $ruleset)
        {
            $this->valid->rules($name, $ruleset);
        }
    }
}
