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
use Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\Core\Entity\FormStageRepository;

class Ushahidi_Validator_Form_Attribute_Update extends Validator
{
    protected $default_error_source = 'form_attribute';
    protected $valid;
    protected $repo;
    protected $form_stage_repo;

    public function __construct(FormAttributeRepository $repo, FormStageRepository $form_stage_repo)
    {
        $this->repo = $repo;
        $this->form_stage_repo = $form_stage_repo;
    }

    protected function getRules()
    {
        return [
            'key' => [
                ['max_length', [':value', 150]],
                ['alpha_dash', [':value', TRUE]],
                [[$this->repo, 'isKeyAvailable'], [':value']]
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
                    'number',
                    'relation',
                    'upload'
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
                    'link',
                    'relation',
                    'media'
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
            'form_stage_id' => [
                ['digit'],
                [[$this->form_stage_repo, 'exists'], [':value']],
                [[$this, 'formStageBelongsToForm'], [':value']],
            ],
            'form_id' => [
                ['digit'],
            ],
        ];
    }

     public function formStageBelongsToForm($value)
    {
        // don't check against nonexistant data
        if (!$value || !isset($this->valid['form_id'])) {
            return true;
        }

        $group = $this->form_stage_repo->get($value);
        return ($group->form_id == $this->valid['form_id']);
    }
}
