<?php

namespace Ushahidi\App\ImportUshahidiV2;

use Illuminate\Support\Collection;
use Ushahidi\Core\Entity\Repository\EntityCreateMany;
use Ushahidi\App\ImportUshahidiV2;

class Importer
{
    protected $sourceType;
    protected $destType;
    protected $mapper;
    protected $mappingRepo;
    protected $destRepo;

    public function __construct(
        string $sourceType,
        Contracts\Mapper $mapper,
        Contracts\ImportMappingRepository $mappingRepo,
        EntityCreateMany $destRepo
    ) {
        $this->sourceType = $sourceType;
        $this->mapper = $mapper;
        $this->mappingRepo = $mappingRepo;
        $this->destRepo = $destRepo;
    }

    /**
     * Run import
     *
     * @todo naming: run? handle? interact? process?
     *
     * @param  $importId            [<description>]
     * @param  Collection $source   source data (list-like indices)
     * @return Collection           detail of objects imported and their mappings
     *         Each member is a standard object that contains keys:
     *              source          the original imported data item (as found in $source)
     *              target          the newly created data item
     *              mapping         an ImportMappingEntity
     */
    public function run(int $importId, Collection $source) : Collection
    {
        $mapper = $this->mapper;

        // Transform objects
        $results = $source->map(function ($item) use ($importId, $mapper) {
            return (object) [
                'source' => $item,
                'target' => $mapper($importId, (array) $item)
            ];
        });

        // Get the resource type from the first model
        $destType = $results->first()->target->getResource();

        // Save objects
        $inserted = $this->destRepo->createMany($results->pluck('target'));

        // Match source and destination ids
        // results in collection source_id -> target_id
        $map_resource_ids = $source->pluck('id')->combine($inserted);
        // Create and save mapping entities 
        $results->each(function ($result) use ($map_resource_ids, $importId, $destType) {
            $sourceId = $result->source->id;
            $result->targetId = $map_resource_ids->get($sourceId);
            $mapping = new ImportUshahidiV2\ImportMapping([
                'import_id' => $importId,
                'source_type' => $this->sourceType,
                'source_id' => $sourceId,
                'dest_type' => $destType,
                'dest_id' => $result->targetId,
            ]);
            $result->mapping = $mapping;
        });

        // Save mappings
        $this->mappingRepo->createMany($results->pluck('mapping'));

        // Return collection of entities with new ids
        return $results;
    }
}
