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
use Illuminate\Support\Collection;

interface ImportMappingRepository
{
    public function create(ImportMapping $model) : int;

    public function createMany(Collection $collection) : array;
}
