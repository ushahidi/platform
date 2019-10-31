<?php

namespace Ushahidi\App\ImportUshahidiV2\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Container\Container;
use Ushahidi\App\Jobs\Job;
use Ushahidi\Core\Entity;
use Ushahidi\App\ImportUshahidiV2;

class ImportForms extends Job
{
    use Concerns\ConnectsToV2DB;

    const BATCH_SIZE = 50;

    protected $importId;
    protected $dbConfig;

    protected $sourceForms = null;
    protected $importedForms = null;

    // Add default attributes
    protected $defaultAttributes = [
        [
            'source_id' => 'title',
            'input' => 'text',
            'label' => 'Report Title',
            'priority' => 1,
            'required' => true,
            'type' => 'title',
            'options' => [],
            'config' => [],
            'cardinality' => 1,
        ],
        [
            'source_id' => 'description',
            'input' => 'text',
            'label' => 'Description',
            'priority' => 2,
            'required' => true,
            'type' => 'description',
            'options' => [],
            'config' => [],
            'cardinality' => 1,
        ],
        [
            'source_id' => 'location_name',
            'label' => 'Location Name',
            'required' => 0,
            'priority' => 0,
            'default' => 0,
            'type' => 'varchar',
            'input' => 'text',
            'options' => [],
            'cardinality' => 1,
        ],
        [
            'source_id' => 'location',
            'label' => 'Location',
            'required' => 0,
            'priority' => 0,
            'default' => 0,
            'type' => 'point',
            'input' => 'location',
            'options' => [],
            'cardinality' => 1,
        ],
        [
            'source_id' => 'verified',
            'label' => 'Verified',
            'required' => 0,
            'priority' => 0,
            'default' => 0,
            'type' => 'int',
            'input' => 'checkbox',
            'options' => [],
            'cardinality' => 1,
        ],
        [
            'source_id' => 'news_source_link',
            'label' => 'News Source Link',
            'required' => 0,
            'priority' => 0,
            'default' => 0,
            'type' => 'varchar',
            'input' => 'text',
            'options' => [],
            'cardinality' => 0,
        ],
        [
            'source_id' => 'video_link',
            'label' => 'External Video Link',
            'required' => 0,
            'priority' => 0,
            'default' => 0,
            'type' => 'varchar',
            'input' => 'video',
            'options' => [],
            'cardinality' => 0,
        ],
        [
            'source_id' => 'photos',
            'label' => 'Photos',
            'required' => 0,
            'priority' => 0,
            'default' => 0,
            'type' => 'media',
            'input' => 'upload',
            'options' => [],
            'cardinality' => 0,
        ],
        [
            'source_id' => 'categories',
            'label' => 'Categories',
            'required' => 0,
            'priority' => 0,
            'default' => 0,
            'type' => 'tags',
            'input' => 'tags',
            'options' => [],
            'cardinality' => 0,
        ],
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     * 
     * TODO1 - add placeholder for import parameters 
     */
    public function __construct(int $importId, array $dbConfig)
    {
        $this->importId = $importId;
        $this->dbConfig = $dbConfig;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        Container $container
    ) {
        // Use container->call to inject each method
        $container->call([$this, 'importForms']);
        $container->call([$this, 'importFields']);
    }

    public function importForms(
        ImportUshahidiV2\Contracts\ImportMappingRepository $mappingRepo,
        Entity\FormRepository $destRepo,
        ImportUshahidiV2\Mappers\FormMapper $mapper
    ) {
        // Set up importer
        $importer = new ImportUshahidiV2\Importer(
            'form',
            $mapper,
            $mappingRepo,
            $destRepo
        );

        // Fetch data
        $sourceData = $this->getConnection()
            ->table('form')
            ->select('form.*')
            ->orderBy('id', 'asc')
            ->get();
        $this->sourceForms = $sourceData;

        $results = $importer->run($this->importId, $sourceData);

        $this->importedForms = $results->map(function ($result) {
            $import = (object) [
                'v2_form' => $result->source,
                'v3_form' => $result->target,
                'v3_formId' => $result->targetId,
            ];
            $import->v3_stageId = $this->createDefaultStageForForm(
                $import->v3_form, $import->v3_formId);
            $this->createDefaultAttributes(
                $import->v3_formId, $import->v3_stageId, $import->v2_form->id);
            return $import;
        });        

        if ($this->importedForms->count() === 0) {
            $this->createDefaultForm();
        }
    }

    protected function createDefaultForm()
    {
        $formRepo = app(Entity\FormRepository::class);
        $mappingRepo = app(ImportUshahidiV2\Contracts\ImportMappingRepository::class);
        $form = new Entity\Form([
            'name' => 'Report',
            'description' => 'Report an incident',
            'require_approval' => true,
            'everyone_can_create' => true,
        ]);
        // Create form
        $formId = $formRepo->create($form);

        // Save form --> survey mapping
        $mappingRepo->create(new ImportUshahidiV2\ImportMapping([
            'import_id' => $this->importId,
            'source_type' => 'form',
            'source_id' => 0,
            'dest_type' => 'form',
            'dest_id' => $formId,
        ]));

        $this->createStagesForForms(collect([$formId => $form]));
    }

    protected function createDefaultStageForForm($form, $formId)
    {
        $stageRepo = app(Entity\FormStageRepository::class);
        $mappingRepo = app(ImportUshahidiV2\Contracts\ImportMappingRepository::class);

        $stageId = $stageRepo->create(new Entity\FormStage([
            'form_id' => $formId,
            'label' => 'Post',
            'priority' => 0,
            'required' => false,
            'type' => 'post',
            'show_when_published' => true,
            'task_is_internal_only' => false,
        ]));

        Log::debug("Stage {stage_id} created for {form}", [
            'stage_id' => $stageId,
            'form' => $form
        ]);

        return $stageId;
    }

    protected function createDefaultAttributes($v3_formId, $v3_stageId, $v2_formId)
    {
        $attrRepo = app(Entity\FormAttributeRepository::class);
        $mappingRepo = app(ImportUshahidiV2\Contracts\ImportMappingRepository::class);

        // Create attributes
        foreach ($this->defaultAttributes as $attr) {
            // Create attribute
            $attrId = $attrRepo->create(new Entity\FormAttribute(
                ['form_stage_id' => $v3_stageId] + $attr
            ));

            Log::debug("Created v3 attribute {attrId} with def {attr}", [
                "attrId" => $attrId,
                "attr" => ['form_stage_id' => $v3_stageId] + $attr
            ]);

            // Create a mapping from attribute to form attribute
            $mappingRepo->create(new ImportUshahidiV2\ImportMapping([
                'import_id' => $this->importId,
                'source_type' => 'incident_column',
                // Combine form id + attribute id
                'source_id' => $v2_formId . '-' . $attr['source_id'],
                'dest_type' => 'form_attributes',
                'dest_id' => $attrId,
            ]));

            Log::debug("Created ImportMapping {import_mapping}", [
                'import_mapping' => [
                    'import_id' => $this->importId,
                    'source_type' => 'incident_column',
                    // Combine form id + attribute id
                    'source_id' => $v2_formId . '-' . $attr['source_id'],
                    'dest_type' => 'form_attributes',
                    'dest_id' => $attrId,
                ]
            ]);
        }
    }

    public function importFields(
        ImportUshahidiV2\Contracts\ImportMappingRepository $mappingRepo,
        Entity\FormAttributeRepository $destRepo,
        ImportUshahidiV2\Mappers\FormFieldAttributeMapper $mapper
    ) {
        // Set up importer
        $importer = new ImportUshahidiV2\Importer(
            'form_field',
            $mapper,
            $mappingRepo,
            $destRepo
        );

        // While there are forms left
        $this->sourceForms->each(function ($v2_form, $idx) use ($importer) {
            // Fetch data
            Log::debug("Importing custom attributes for v2 form {form}", [
                "form" => $v2_form
            ]);

            $sourceData = $this->getConnection()
                ->table('form_field')
                ->select(
                    'form_field.*',
                    'datatype.option_value AS field_datatype',
                    'hidden.option_value AS field_hidden',
                    'toggle.option_value AS field_toggle'
                )
                ->leftJoin('form_field_option as datatype', function ($join) {
                    $join->on('datatype.form_field_id', '=', 'form_field.id');
                    $join->where('datatype.option_name', '=', 'field_datatype');
                })
                ->leftJoin('form_field_option as hidden', function ($join) {
                    $join->on('hidden.form_field_id', '=', 'form_field.id');
                    $join->where('hidden.option_name', '=', 'field_hidden');
                })
                ->leftJoin('form_field_option as toggle', function ($join) {
                    $join->on('toggle.form_field_id', '=', 'form_field.id');
                    $join->where('toggle.option_name', '=', 'field_toggle');
                })
                // Exclude divider fields
                ->where('form_id', $v2_form->id)
                ->whereNotIn('field_type', [8, 9])
                ->orderBy('field_position', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            Log::debug("Importing v2 form id {form_id} attributes, attributes follow", [
                "form_id" => $v2_form->id
            ]);
            $sourceData->each(function ($v2_attr, $arrayId) {
                Log::debug("including v2 attribute {}", [$v2_attr]);
            });

            $created = $importer->run($this->importId, $sourceData);

            $created->each(function ($v3_attr, $v3_id) {
                Log::debug("Created v3 attribute {v3_id}:{v3_attr}", [
                    'v3_id' => $v3_id,
                    'v3_attr' => $v3_attr
                ]);
            });
        });
    }
}
