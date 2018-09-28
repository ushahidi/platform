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

use Kohana\Validation\Validation;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\Core\Entity\FormStageRepository;

class Update extends Validator
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
        $type = $this->validation_engine->getFullData('type');

        return [
            'key' => [
                ['max_length', [':value', 150]],
                ['alpha_dash', [':value', true]],
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
                    'upload',
                    'video',
                    'markdown',
                    'tags',
                ]]],
            ],
            'type' => [
                ['in_array', [':value', [
                    'decimal',
                    'int',
                    'geometry',
                    'text',
                    'varchar',
                    'markdown',
                    'point',
                    'datetime',
                    'link',
                    'relation',
                    'media',
                    'title',
                    'description',
                    'tags',
                ]]],
                [[$this, 'checkForDuplicates'], [':validation', ':value']],
            ],
            'required' => [
                ['in_array', [':value', [true, false]]],
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
            'response_private' => [
                [[$this, 'canMakePrivate'], [':value', $type]]
            ]
        ];
    }

    public function checkForDuplicates(Validation $validation, $value)
    {
        $form_stage_id = $this->validation_engine->getFullData('form_stage_id');
        $form_id = $this->form_stage_repo->getFormByStageId($form_stage_id);
        $id = $this->validation_engine->getFullData('id');

        if ($value === 'description' || $value === 'title') {
            $attributes = $this->repo->getAllByType($value, $form_id, $id);
            if (count($attributes) === 0) {
                 return true;
            }
            return $validation->error('type', 'duplicateTypes', [$value]);
        }
        return true;
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

    public function canMakePrivate($value, $type)
    {
        // If input type is tags, then attribute cannot be private
        if ($type === 'tags' && $value !== false) {
            return false;
        }

        return true;
    }
}
