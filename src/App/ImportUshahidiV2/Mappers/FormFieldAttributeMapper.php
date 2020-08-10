<?php

namespace Ushahidi\App\ImportUshahidiV2\Mappers;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\FormAttribute;
use Ushahidi\Core\Entity\FormStageRepository;
use Ushahidi\App\ImportUshahidiV2\Contracts\Mapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportDataInspectionTools;

class FormFieldAttributeMapper implements Mapper
{
    protected $mappingRepo;
    protected $stageRepo;
    protected $inspectionTools;

    // field_type -> attribute input map
    const TYPE_INPUT_MAP = [
        1 => 'text',
        2 => 'textarea',
        3 => 'date',
        5 => 'radio',
        6 => 'checkboxes',
        7 => 'select',
        // 8 => 'divider_start',
        // 9 => 'divider_end'
    ];

    // field_datatype -> attribute type map
    const DATATYPE_TYPE_MAP = [
        'text' => 'text',
        'numeric' => 'varchar',     // safest default
        'email' => 'varchar',
        'phonenumber' => 'varchar',
    ];

    public function __construct(
        ImportMappingRepository $mappingRepo,
        FormStageRepository $stageRepo,
        ImportDataInspectionTools $inspectionTools
    ) {
        $this->mappingRepo = $mappingRepo;
        $this->stageRepo = $stageRepo;
        $this->inspectionTools = $inspectionTools;
    }

    public function __invoke(int $importId, array $input) : Entity
    {
        list($attrInput, $type) = $this->getInputAndType(
            $input['id'],
            $input['field_type'],
            $input['field_datatype'],
            $input['field_isdate']
        );
        list($default, $options) = $this->getDefaultAndOptions(
            $attrInput,
            $input['field_default']
        );

        return new FormAttribute([
            'form_stage_id' => $this->getFormStageId($importId, $input['form_id']),
            'label' => $input['field_name'],
            'required' => $input['field_required'],
            'priority' => $input['field_position'],
            'default' => $default,
            'type' => $type,
            'input' => $attrInput,
            'options' => $options,
            'cardinality' => in_array($attrInput, ['checkboxes', 'select']) ? 0 : 1,
            'response_private' => !$input['field_ispublic_visible'],
            // We can't map field_ispublic_submit to anything right now
            // Ideally we should group those fields in a stage w/ task_is_internal_only = 1
        ]);
    }

    public function getInputAndType($fieldId, $fieldType, $fieldDataType, $isDate)
    {
        $type = ($fieldDataType && isset(self::DATATYPE_TYPE_MAP[$fieldDataType])) ?
            self::DATATYPE_TYPE_MAP[$fieldDataType] : 'varchar';
        $attrInput = ($fieldType && isset(self::TYPE_INPUT_MAP[$fieldType])) ?
            self::TYPE_INPUT_MAP[$fieldType] : 'text';

        // if field datatype is 'numeric', study which is the best corresponding type
        if ($fieldDataType == 'numeric') {
            $type = $this->inspectionTools->suggestNumberStorage($fieldId);
        }

        // if input is date, use datetime storage type
        if ($attrInput == 'date') {
            $type = 'datetime';
        }

        // check field_isdate -> makes it a date field
        if ($isDate == 1) {
            $type = 'datetime';
            $attrInput = 'date';
        }

        return [$attrInput, $type];
    }

    protected function getDefaultAndOptions($attrInput, $fieldDefault)
    {
        if (in_array($attrInput, ['checkboxes', 'select', 'radio'])) {
            // Parse default values from options
            // Options are comma separated, but may include a default value
            // For example in "value1, value2, value3::value3".
            // The default is value3.
            $defaultAndOptions = explode('::', $fieldDefault);
            $default = count($defaultAndOptions) > 1 ? $defaultAndOptions[1] : '';
            $options = array_map('trim', explode(',', $defaultAndOptions[0]));
        } else {
            $default = $fieldDefault;
            $options = null;
        }

        return [$default, $options];
    }

    protected function getFormStageId($importId, $formId)
    {
        $newFormId = $this->mappingRepo->getDestId($importId, 'form', $formId);
        // Get post stage for new form
        $stage = $this->stageRepo->getPostStage($newFormId);

        return $stage->id;
    }
}
