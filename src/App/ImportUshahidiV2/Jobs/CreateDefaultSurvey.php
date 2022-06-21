<?php

namespace Ushahidi\App\ImportUshahidiV2\Jobs;

use Illuminate\Support\Facades\Log;
use Ushahidi\Core\Entity;
use Ushahidi\App\Jobs\Job;
use Ushahidi\App\ImportUshahidiV2;

class CreateDefaultSurvey extends Job
{
    protected $importId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $importId)
    {
        $this->importId = $importId;
    }

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
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        ImportUshahidiV2\Contracts\ImportMappingRepository $mappingRepo,
        Entity\FormRepository $formRepo,
        Entity\FormStageRepository $stageRepo,
        Entity\FormAttributeRepository $attrRepo
    ) {
        // Create form
        $formId = $formRepo->create(new Entity\Form([
            'name' => 'Report',
            'description' => 'Report an incident',
            'require_approval' => true,
            'everyone_can_create' => true,
        ]));

        // Create stage
        $stageId = $stageRepo->create(new Entity\FormStage([
            'form_id' => $formId,
            'label' => 'Post',
            'priority' => 0,
            'required' => false,
            'type' => 'post',
            'show_when_published' => true,
            'task_is_internal_only' => false,
        ]));

        // Create attributes
        foreach ($this->defaultAttributes as $attr) {
            $attrId = $attrRepo->create(new Entity\FormAttribute(
                ['form_stage_id' => $stageId] + $attr
            ));

            $mappingRepo->create(new ImportUshahidiV2\ImportMapping([
                'import_id' => $this->importId,
                'source_type' => 'incident_column',
                // Combine form id + attribute id
                'source_id' => '0-' . $attr['source_id'],
                'dest_type' => 'form_attribute',
                'dest_id' => $attrId,
            ]));
            // Hack. Map form id 1 too because v2 treats them as 1 form.
            $mappingRepo->create(new ImportUshahidiV2\ImportMapping([
                'import_id' => $this->importId,
                'source_type' => 'incident_column',
                // Combine form id + attribute id
                'source_id' => '1-' . $attr['source_id'],
                'dest_type' => 'form_attribute',
                'dest_id' => $attrId,
            ]));
        }

        // Save form --> survey mapping
        $mappingRepo->create(new ImportUshahidiV2\ImportMapping([
            'import_id' => $this->importId,
            'source_type' => 'form',
            'source_id' => 0,
            'dest_type' => 'form',
            'dest_id' => $formId,
        ]));
        // Hack. Map form id 1 too because v2 treats them as 1 form.
        $mappingRepo->create(new ImportUshahidiV2\ImportMapping([
            'import_id' => $this->importId,
            'source_type' => 'form',
            'source_id' => 1,
            'dest_type' => 'form',
            'dest_id' => $formId,
        ]));
    }
}
