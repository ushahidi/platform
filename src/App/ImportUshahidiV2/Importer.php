<?php

namespace Ushahidi\App\ImportUshahidiV2;

use Illuminate\Support\Collection;
use Ushahidi\Core\Entity\Repository\EntityCreateMany;
use Ushahidi\App\ImportUshahidiV2;
use Illuminate\Support\Facades\Log;

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
    public function run(Import $import, Collection $source) : Collection
    {
        $importId = $import->id;
        $mapper = $this->mapper;

        /* Empty input -> empty output */
        if ($source->count() == 0) {
            return new Collection();
        }

        // Transform objects
        $results = $source->map(function ($item) use ($import, $mapper) {
            $mapped = $mapper($import, (array) $item);
            return (object) [
                'source' => $item,
                'target' => $mapped['result'],
                'metadata' => $mapped['metadata'] ?? null,
            ];
        });

        // Filter out objects that didn't map successfully (obj->target == null)
        list($failed, $mapped) = $results->partition(function ($v) {
            return $v->target == null;
        });

        if ($failed->count() > 0) {
            Log::debug('[Import] The following mappings failed {failed}', [
                'failed' => $failed
            ]);
        }

        // If no successful mappings, return empty collection
        if ($mapped->count() == 0) {
            return $mapped;
        }

        // Rebuild source list from successful transformation objects
        // ( so they position-match when we create $map_resource_ids a few lines down )
        $source = $mapped->pluck('source');

        // Get the resource type from the first model
        $destType = $mapped->first()->target->getResource();

        // Save successfully mapped objects
        $inserted = $this->destRepo->createMany($mapped->pluck('target'));

        // Match source and destination ids
        // results in collection source_id -> target_id
        $map_resource_ids = $source->pluck('id')->combine($inserted);
        // Create and save mapping entities
        $mapped->each(function ($result) use ($map_resource_ids, $importId, $destType) {
            $sourceId = $result->source->id;
            $result->targetId = $map_resource_ids->get($sourceId);
            $mapping = new ImportUshahidiV2\ImportMapping([
                'import_id' => $importId,
                'source_type' => $this->sourceType,
                'source_id' => $sourceId,
                'dest_type' => $destType,
                'dest_id' => $result->targetId,
                'metadata' => $result->metadata //json_encode($result->metadata)
            ]);
            $result->mapping = $mapping;
        });

        // Save successful mappings
        $this->mappingRepo->createMany($mapped->pluck('mapping'));

        // Return collection of entities with new ids
        return $mapped;
    }
}
