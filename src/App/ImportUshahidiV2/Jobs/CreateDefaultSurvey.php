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
            'key' => 'title',
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
            'key' => 'description',
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
            'key' => 'date',
            // 'original_id' => 'date',
            'label' => 'Date',
            'required' => 0,
            'priority' => 0,
            'default' => 0,
            'type' => 'datetime',
            'input' => 'datetime',
            'options' => [],
            'cardinality' => 1,
        ],
        [
            'key' => 'location_name',
            // 'original_id' => 'location_name',
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
            'key' => 'location',
            // 'original_id' => 'location',
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
            'key' => 'verified',
            // 'original_id' => 'verified',
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
            'key' => 'news_source_link',
            // 'original_id' => 'news',
            'label' => 'News Source Link',
            'required' => 0,
            'priority' => 0,
            'default' => 0,
            'type' => 'link',
            'input' => 'text',
            'options' => [],
            'cardinality' => 0,
        ],
        [
            'key' => 'video_link',
            // 'original_id' => 'news',
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
            'key' => 'photos',
            // 'original_id' => 'news',
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
            'key' => 'categories',
            // 'original_id' => 'news',
            'label' => 'Photos',
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
            'name' => 'Incident',
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
            $attrRepo->create(new Entity\FormAttribute(
                ['form_stage_id' => $stageId] + $attr
            ));
        }

        // Save form --> survey mapping
        $mappingRepo->create(new ImportUshahidiV2\ImportMapping([
            'import_id' => $this->importId,
            'source_type' => 'form',
            'source_id' => 0,
            'dest_type' => 'survey',
            'dest_id' => $formId,
        ]));
    }
}
