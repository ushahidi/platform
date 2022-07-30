<?php

/**
 * Import Repo
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2020 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\V2\Repositories;

use Ushahidi\App\V2\ImportSourceData;
use Ushahidi\App\V2\Contracts\ImportSourceDataRepository as ImportSourceDataRepositoryContract;

class ImportSourceDataRepository implements ImportSourceDataRepositoryContract
{
    public function create(ImportSourceData $model) : int
    {
        return $model->save() ? $model->id : false;
    }

    public function insert(array $models)
    {
        ImportSourceData::insert($models);
    }
}
