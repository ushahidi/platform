<?php

/**
 * Import Mapping Repo
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\ImportUshahidiV2\Contracts;

use Ushahidi\App\ImportUshahidiV2\ImportMapping;
use Ushahidi\App\ImportUshahidiV2\ManifestSchemas\Mappings as ManifestMappings;

use Illuminate\Support\Collection;

interface ImportMappingRepository
{
    public function create(ImportMapping $model) : int;

    public function createMany(Collection $collection) : array;

    public function hasMapping(int $importId, string $sourceType, $sourceId);

    public function getMapping(int $importId, string $sourceType, $sourceId);

    public function getDestId(int $importId, string $sourceType, $sourceId);

    public function getMetadata(int $importId, string $sourceType, $sourceId);

    public function getAllMappingIDs(int $importId, string $sourceType);
}
