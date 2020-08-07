<?php

namespace Ushahidi\App\ImportUshahidiV2\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Container\Container;

use Ushahidi\App\Jobs\Job;
use Ushahidi\Core\Entity;
use Ushahidi\App\ImportUshahidiV2;
use Ushahidi\App\ImportUshahidiV2\ManifestSchemas\ImportParameters;

class ImportForms extends Job
{
    use Concerns\ConnectsToV2DB;

    const BATCH_SIZE = 50;

    protected $importId;
    protected $dbConfig;
    protected $mappingRepo;
    protected $formAttributeRepo;
    protected $extraParams;

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

    protected $geometryAttribute = [
        'source_id' => 'geometry',
        'label' => 'Geometry',
        'required' => 0,
        'priority' => 0,
        'default' => 0,
        'type' => 'geometry',
        'input' => 'geometry',      // TODO: check this with the client
        'options' => [],
        'cardinality' => 0,
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $importId, array $dbConfig, ImportParameters $extraParams)
    {
        $this->importId = $importId;
        $this->dbConfig = $dbConfig;
        $this->extraParams = $extraParams;
    }

    protected function lookupAttributeByKey($key)
    {
        /* Look up attribute by key */
        Log::debug("looking up attribute by key: {}", [$key]);
        $attr = $this->formAttributeRepo->getByKey($key);
        if (!$attr) {
            throw new Exception("Attribute with key {$am->from->key} not found");
        }
        Log::debug("found attribute: {}", [$attr]);
        return $attr->id;
    }

    protected function sourceHasGeometries()
    {
        $count = $this->getConnection()
            ->table('geometry')
            ->count();
        return $count > 0;
    }

    /**
     * Process the extra parameters that may have been provided for the import job
     */
    protected function processExtraParams()
    {
        /* Gather configured mappings */
        $formMaps = $this->extraParams->getFormMappings();

        Log::debug("form mappings preview: {}", [$formMaps]);

        /* Resolve the mappings */
        $importMappings = (new Collection($formMaps))->map(function ($m) {
            Log::debug("processing form mapping: {}", [$m]);
            if (!$m->from->id || !$m->to->id) {
                throw new Exception("Category mapping is from or to id");
            }

            // Incident column mappings
            $incidentColumns = new Collection($m->incidentColumns->asImportMappings($m->from->id));
            $columnMappings = $incidentColumns->map(function ($to, $from) {
                if ($to != null && $to->key && !$to->id) {
                    $to->id = $this->lookupAttributeByKey($to->key);
                }
                Log::debug("new incident column mapping: {} -> {}", [$from, $to]);
                if ($to != null && $to->id) {
                    // Create mapping
                    return new ImportUshahidiV2\ImportMapping([
                        'import_id' => $this->importId,
                        'source_type' => 'incident_column',
                        'source_id' => $from,
                        'dest_type' => 'form_attributes',
                        'dest_id' => $to->id,
                        'established_by' => 'import-config',
                    ]);
                }
            })->filter(function ($v) {
                return $v != null;
            });

            // Custom form fields
            $attrMappings = (new Collection($m->attributes))->map(function ($am) {
                Log::debug("procesing attribute mapping: {}", [$am]);
                if ($am->from->key && !$am->from->id) {
                    $am->from->id = $this->lookupAttributeByKey($am->from->key);
                }
                if ($am->to->key && !$am->to->id) {
                    $am->to->id = $this->lookupAttributeByKey($am->to->key);
                }
                Log::debug("new attribute mapping: {}", [$am]);
                if ($am->from->id && $am->to->id) {
                    // Create mapping
                    return new ImportUshahidiV2\ImportMapping([
                        'import_id' => $this->importId,
                        'source_type' => 'form_field',
                        'source_id' => $am->from->id,
                        'dest_type' => 'form_attributes',
                        'dest_id' => $am->to->id,
                        'established_by' => 'import-config',
                    ]);
                }
            })->filter(function ($v) {
                return $v != null;
            });

            // Create mapping
            $formMapping = new ImportUshahidiV2\ImportMapping([
                'import_id' => $this->importId,
                'source_type' => 'form',
                'source_id' => $m->from->id,
                'dest_type' => 'form',
                'dest_id' => $m->to->id,
                'established_by' => 'import-config',
            ]);

            return $columnMappings->merge($attrMappings)->merge([$formMapping]);
        });

        $importMappings = $importMappings->reduce(function ($carry, $item) {
            return $carry->merge($item);
        }, new Collection());

        /* Create mappings */
        Log::debug("create mappings preview:");
        $importMappings->each(function ($m) {
            Log::debug("{}", [$m->toArray()]);
        });

        $this->mappingRepo->createMany($importMappings);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        Container $container
    ) {

        $this->formAttributeRepo = $container->make('Ushahidi\Core\Entity\FormAttributeRepository');
        $this->mappingRepo = $container->make('Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository');

        // Process provided extra parameters
        $this->processExtraParams();

        // Use container->call to inject each method
        $container->call([$this, 'importForms']);
        $container->call([$this, 'importFields']);
    }

    public function importForms(
        ImportUshahidiV2\Contracts\ImportMappingRepository $mappingRepo,
        Entity\FormRepository $destRepo,
        Entity\FormAttributeRepository $formAttributeRepo,
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

        // Exclude from the list form mappings that are already present (i.e. because they have been configured)
        $sourceData = $sourceData->filter(function ($v2_form) use ($mappingRepo) {
            return !($mappingRepo->hasMapping($this->importId, 'form', $v2_form->id));
        });

        $this->sourceForms = $sourceData;

        $results = $importer->run($this->importId, $sourceData);

        $this->importedForms = $results->map(function ($result) {
            $import = (object) [
                'v2_form' => $result->source,
                'v3_form' => $result->target,
                'v3_formId' => $result->targetId,
            ];
            $import->v3_stageId = $this->createDefaultStageForForm(
                $import->v3_form,
                $import->v3_formId
            );
            $this->createDefaultAttributes(
                $import->v3_formId,
                $import->v3_stageId,
                $import->v2_form->id
            );
            return $import;
        });

        // davidlosada: Not entirely clear why this is needed
        // .. commenting out as it interferes in the scenario when
        // .. existing forms have been pre-mapped
        // if ($this->importedForms->count() === 0) {
        //     $this->createDefaultForm();
        // }
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
            'dest_type' => 'forms',
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

        // Create geometry attribute
        // but only if there are geometries created
        if ($this->sourceHasGeometries()) {
            // Create attribute
            $attrId = $attrRepo->create(new Entity\FormAttribute(
                ['form_stage_id' => $v3_stageId] + $this->geometryAttribute
            ));

            Log::debug("Created v3 attribute {attrId} with def {attr}", [
                "attrId" => $attrId,
                "attr" => ['form_stage_id' => $v3_stageId] + $this->geometryAttribute
            ]);

            // Create a mapping from attribute to form attribute
            $mappingRepo->create(new ImportUshahidiV2\ImportMapping([
                'import_id' => $this->importId,
                'source_type' => 'incident_column',
                // Combine form id + attribute id
                'source_id' => $v2_formId . '-' . $this->geometryAttribute['source_id'],
                'dest_type' => 'form_attributes',
                'dest_id' => $attrId,
            ]));

            Log::debug("Created ImportMapping {import_mapping}", [
                'import_mapping' => [
                    'import_id' => $this->importId,
                    'source_type' => 'incident_column',
                    // Combine form id + attribute id
                    'source_id' => $v2_formId . '-' . $this->geometryAttribute['source_id'],
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
