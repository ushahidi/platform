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

        $imported = 0;
        $batch = 0;
        // While there are users left
        while (true) {
            // Fetch data
            $sourceData = $this->getConnection()
                ->table('form')
                ->select('form.*')
                ->limit(self::BATCH_SIZE)
                ->offset($batch * self::BATCH_SIZE)
                ->orderBy('id', 'asc')
                ->get();

            // If there are no more users
            if ($sourceData->isEmpty()) {
                // Break out of the loop
                break;
            }

            $forms = $importer->run($this->importId, $sourceData);

            $this->createStagesForForms($forms);

            $imported += $forms->count();
            $batch++;
        }

        if ($imported === 0) {
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

    protected function createStagesForForms($forms)
    {
        $stageRepo = app(Entity\FormStageRepository::class);
        $mappingRepo = app(ImportUshahidiV2\Contracts\ImportMappingRepository::class);
        $forms->each(function ($form, $formId) use ($stageRepo, $mappingRepo) {
            // Create a single stage for every form
            $stageId = $stageRepo->create(new Entity\FormStage([
                'form_id' => $formId,
                'label' => 'Post',
                'priority' => 0,
                'required' => false,
                'type' => 'post',
                'show_when_published' => true,
                'task_is_internal_only' => false,
            ]));

            $this->createDefaultAttributes($formId, $stageId);
        });
    }

    protected function createDefaultAttributes($formId, $stageId)
    {
        $attrRepo = app(Entity\FormAttributeRepository::class);
        $mappingRepo = app(ImportUshahidiV2\Contracts\ImportMappingRepository::class);

        // Create attributes
        foreach ($this->defaultAttributes as $attr) {
            // Create attribute
            $attrId = $attrRepo->create(new Entity\FormAttribute(
                ['form_stage_id' => $stageId] + $attr
            ));

            // Create a mapping from attribute to form attribute
            $mappingRepo->create(new ImportUshahidiV2\ImportMapping([
                'import_id' => $this->importId,
                'source_type' => 'incident_column',
                // Combine form id + attribute id
                'source_id' => $formId . '-' . $attr['source_id'],
                'dest_type' => 'form_attributes',
                'dest_id' => $attrId,
            ]));
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

        $imported = 0;
        $batch = 0;
        // While there are users left
        while (true) {
            // Fetch data
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
                ->whereNotIn('field_type', [8, 9])
                ->limit(self::BATCH_SIZE)
                ->offset($batch * self::BATCH_SIZE)
                ->orderBy('form_id', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // If there are no more users
            if ($sourceData->isEmpty()) {
                // Break out of the loop
                break;
            }

            $created = $importer->run($this->importId, $sourceData);

            // Add to count
            $imported += $created->count();
            $batch++;
        }
    }
}
