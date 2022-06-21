<?php

namespace Ushahidi\App\ImportUshahidiV2\Mappers;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\FormAttribute;
use Ushahidi\Core\Entity\FormStageRepository;
use Ushahidi\App\ImportUshahidiV2\Import;
use Ushahidi\App\ImportUshahidiV2\Jobs\ImportForms;
use Ushahidi\App\ImportUshahidiV2\Contracts\Mapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportDataTools;

use Illuminate\Support\Facades\Log;

class FormFieldAttributeMapper implements Mapper
{
    protected $mappingRepo;
    protected $stageRepo;
    protected $dataTools;

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
        ImportDataTools $dataTools
    ) {
        $this->mappingRepo = $mappingRepo;
        $this->stageRepo = $stageRepo;
        $this->dataTools = $dataTools;
    }

    public function __invoke(Import $import, array $input) : array
    {
        $importId = $import->id;
        list($attrInput, $type, $meta) = $this->getInputAndType(
            $input['id'],
            $input['field_type'],
            $input['field_datatype'],
            $input['field_isdate']
        );
        list($default, $options) = $this->getDefaultAndOptions(
            $attrInput,
            $input['field_default']
        );

        $result = new FormAttribute([
            'form_stage_id' => $this->getFormStageId($importId, $input['form_id']),
            'label' => $input['field_name'],
            'required' => $input['field_required'],
            # these custom attributes should be rendered after the default ones
            'priority' => $input['field_position'] + ImportForms::DEAFULT_ATTRIBUTES_LAST_PRIORITY,
            'default' => $default,
            'type' => $type,
            'input' => $attrInput,
            'options' => $options,
            'cardinality' => in_array($attrInput, ['checkboxes', 'select']) ? 0 : 1,
            'response_private' => !$input['field_ispublic_visible'],
            // We can't map field_ispublic_submit to anything right now
            // Ideally we should group those fields in a stage w/ task_is_internal_only = 1
        ]);

        return [
            'result' => $result,
            'metadata' => $meta
        ];
    }

    public function getInputAndType($fieldId, $fieldType, $fieldDataType, $isDate)
    {
        $type = ($fieldDataType && isset(self::DATATYPE_TYPE_MAP[$fieldDataType])) ?
            self::DATATYPE_TYPE_MAP[$fieldDataType] : 'varchar';
        $attrInput = ($fieldType && isset(self::TYPE_INPUT_MAP[$fieldType])) ?
            self::TYPE_INPUT_MAP[$fieldType] : 'text';
        $meta = null;

        // if field datatype is 'numeric', study which is the best corresponding type
        if ($fieldDataType == 'numeric') {
            $type = $this->dataTools->suggestNumberStorage($fieldId);
            $meta = (object) ['encode' => [ 'type' => $type ]];
        }

        // if input is date, use datetime storage type
        if ($attrInput == 'date' || $isDate == 1) {
            $type = 'datetime';
            $attrInput = 'date';
            $formats = $this->dataTools->tryDateDecodeFormats($fieldId);
            $meta = (object) ['decode' => [ 'datetime' => [ 'format_study' => $formats ]]];
        }

        return [$attrInput, $type, $meta];
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
