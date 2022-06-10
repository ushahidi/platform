<?php

namespace Tests\Unit\App\ImportUshahidiV2\Mappers;

use Ushahidi\App\ImportUshahidiV2\Jobs\ImportForms;
use Ushahidi\App\ImportUshahidiV2\Mappers\FormFieldAttributeMapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportDataTools;
use Ushahidi\Core\Entity\FormAttribute;
use Ushahidi\Core\Entity\FormStage;
use Ushahidi\Core\Entity\FormStageRepository;
use Tests\Unit\App\ImportUshahidiV2\ImportMock;
use Tests\TestCase;
use Mockery as M;
use Faker;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class FormFieldAttributeMapperTest extends TestCase
{
    /**
     * @dataProvider formFieldProvider
     */
    public function testMap($input, $expected)
    {
        $mappingRepo = M::mock(ImportMappingRepository::class);
        $mappingRepo->shouldReceive('getDestId')
            ->with(1, 'form', 1)
            ->andReturn(11);

        $stageRepo = M::mock(FormStageRepository::class);
        $stageRepo->shouldReceive('getPostStage')
            ->with(11)
            ->andReturn(new FormStage(['id' => 110]));

        $dataTools = M::mock(ImportDataTools::class);
        $dataTools->shouldReceive('suggestNumberStorage')
            ->with(3)
            ->andReturn('int');
        $dataTools->shouldReceive('tryDateDecodeFormats')
            ->andReturn([ 'd#m#Y' => 1.0 , 'm#d#Y' => 0.9 ]);

        $mapper = new FormFieldAttributeMapper(
            $mappingRepo,
            $stageRepo,
            $dataTools
        );

        $import = ImportMock::forId(1);
        $result = $mapper($import, $input);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('result', $result);
        $attr = $result['result'];
        
        $this->assertInstanceOf(FormAttribute::class, $attr);
        $this->assertArraySubset(
            $expected,
            $attr->asArray(),
            true,
            "Attribute didn't match. Actual data was: ". var_export($attr->asArray(), true)
        );
    }


    public function formFieldProvider()
    {
        return [
            'datefield' => [
                'input' => [
                    'id' => 1,
                    'form_id' => 1,
                    'field_type' => 1,
                    'field_datatype' => 3,
                    'field_isdate' => 1,
                    'field_default' => '',
                    'field_name' => 'A date field',
                    'field_required' => 1,
                    'field_position' => 77,
                    'field_ispublic_visible' => 1
                ],
                'expected' => [
                    'label' => 'A date field',
                    'instructions' => null,
                    'input' => 'date',
                    'type' => 'datetime',
                    'required' => true,
                    'default' => '',
                    'priority' => ImportForms::DEAFULT_ATTRIBUTES_LAST_PRIORITY + 77,
                    'options' => null,
                    'cardinality' => 1,
                    'config' => [],
                    'form_stage_id' => 110,
                    'response_private' => false,
                ],
            ],
            'textfield' => [
                'input' => [
                    'id' => 2,
                    'form_id' => 1,
                    'field_type' => 1,
                    'field_datatype' => 'text',
                    'field_isdate' => 0,
                    'field_default' => '',
                    'field_name' => 'A text field',
                    'field_required' => 0,
                    'field_position' => 66,
                    'field_ispublic_visible' => 0
                ],
                'expected' => [
                    'label' => 'A text field',
                    'instructions' => null,
                    'input' => 'text',
                    'type' => 'text',
                    'required' => false,
                    'default' => '',
                    'priority' => ImportForms::DEAFULT_ATTRIBUTES_LAST_PRIORITY + 66,
                    'options' => null,
                    'cardinality' => 1,
                    'config' => [],
                    'form_stage_id' => 110,
                    'response_private' => true,
                ],
            ],
            'checkboxfield' => [
                'input' => [
                    'id' => 3,
                    'form_id' => 1,
                    'field_type' => 6,
                    'field_datatype' => 'numeric',
                    'field_isdate' => 0,
                    'field_default' => '1,2,3::2',
                    'field_name' => 'A field',
                    'field_required' => 1,
                    'field_position' => 77,
                    'field_ispublic_visible' => 1
                ],
                'expected' => [
                    'label' => 'A field',
                    'instructions' => null,
                    'input' => 'checkboxes',
                    'type' => 'int',
                    'required' => true,
                    'default' => '2',
                    'priority' => ImportForms::DEAFULT_ATTRIBUTES_LAST_PRIORITY + 77,
                    'options' => ['1','2','3'],
                    'cardinality' => 0,
                    'config' => [],
                    'form_stage_id' => 110,
                    'response_private' => false,
                ],
            ],
            'radio' => [
                'input' => [
                    'id' => 4,
                    'form_id' => 1,
                    'field_type' => 5,
                    'field_datatype' => 'text',
                    'field_isdate' => 0,
                    'field_default' => 'one,two,three::two',
                    'field_name' => 'A field',
                    'field_required' => 1,
                    'field_position' => 77,
                    'field_ispublic_visible' => 1
                ],
                'expected' => [
                    'label' => 'A field',
                    'instructions' => null,
                    'input' => 'radio',
                    'type' => 'text',
                    'required' => true,
                    'default' => 'two',
                    'priority' => ImportForms::DEAFULT_ATTRIBUTES_LAST_PRIORITY + 77,
                    'options' => ['one','two','three'],
                    'cardinality' => 1,
                    'config' => [],
                    'form_stage_id' => 110,
                    'response_private' => false,
                ],
            ],
            'unknown' => [
                'input' => [
                    'id' => 5,
                    'form_id' => 1,
                    'field_type' => 55,
                    'field_datatype' => 'junk',
                    'field_isdate' => 0,
                    'field_default' => 'test',
                    'field_name' => 'A field',
                    'field_required' => 1,
                    'field_position' => 77,
                    'field_ispublic_visible' => 1
                ],
                'expected' => [
                    'label' => 'A field',
                    'instructions' => null,
                    'input' => 'text',
                    'type' => 'varchar',
                    'required' => true,
                    'default' => 'test',
                    'priority' => ImportForms::DEAFULT_ATTRIBUTES_LAST_PRIORITY + 77,
                    'options' => null,
                    'cardinality' => 1,
                    'config' => [],
                    'form_stage_id' => 110,
                    'response_private' => false,
                ],
            ]
        ];
    }
}
