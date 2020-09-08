<?php

/**
 * Import Mapping Repo
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\ImportUshahidiV2\Repositories;

use Ushahidi\App\ImportUshahidiV2\ImportMapping;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository as ImportMappingRepositoryContract;
use Ushahidi\App\ImportUshahidiV2\ManifestSchemas\Mappings as ManifestMappings;
use Ushahidi\Core\Entity\TagRespository;
use Ushahidi\Core\Entity\FormRepository;
use Ushahidi\Core\Entity\FormAttributeRepository;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ImportMappingRepository /*extends EloquentRepository*/ implements ImportMappingRepositoryContract
{
    /**
     * Cache lifetime in minutes
     * @var integer
     */
    protected $cache_lifetime = 10;

    public function create(ImportMapping $model) : int
    {
        $id = $model->save() ? $model->id : false;

        if ($id) {
            $key = "mapping.{$model->importId}.{$model->sourceType}.{$model->sourceId}";
            Cache::tags(['import_mapping'])->put($key, $model->destId);
        }

        return $id;
    }

    public function createMany(Collection $collection) : array
    {
        $insertId = ImportMapping::insert(
            $collection->map(function ($model) {
                return $model->toArray();
            })->all()
        );

        $insertId = ImportMapping::resolveConnection()->getPdo()->lastInsertId();

        // Save mappings to cache
        $collection->each(function ($item) {
            $key = "mapping.{$item->importId}.{$item->sourceType}.{$item->sourceId}";
            Cache::tags(['import_mapping'])->put($key, $item->destId);
        });

        return range($insertId, $insertId + $collection->count() - 1);
    }

    public function hasMapping(int $importId, string $sourceType, $sourceId)
    {
        $match = $this->getMapping($importId, $sourceType, $sourceId);
        return ($match != null);
    }

    public function getMapping(int $importId, string $sourceType, $sourceId)
    {
        return ImportMapping::where([
            'import_id' => $importId,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ])->first();
    }

    public function getDestId(int $importId, string $sourceType, $sourceId)
    {
        $key = "mapping.$importId.$sourceType.$sourceId";

        return Cache::tags(['import_mapping'])->remember(
            $key,
            $this->cache_lifetime,
            function () use ($importId, $sourceType, $sourceId) {
                return ImportMapping::where([
                    'import_id' => $importId,
                    'source_type' => $sourceType,
                    'source_id' => $sourceId,
                ])->value('dest_id');
            }
        );
    }

    public function getMetadata(int $importId, string $sourceType, $sourceId)
    {
        $result = $this->getMapping($importId, $sourceType, $sourceId);
        if ($result === null) {
            return null;
        }

        $result = $result->value('metadata');

        if ($result === null) {
            return null;
        } else {
            return json_decode($result, true);
        }
    }

    public function getAllMappingIDs(int $importId, string $sourceType)
    {
        $rawMappings = ImportMapping::where([
            'import_id' => $importId,
            'source_type' => $sourceType
        ])->get();

        return $rawMappings->mapWithKeys(function ($m) {
            return [$m['source_id'] => $m['dest_id']];
        });
    }
}
