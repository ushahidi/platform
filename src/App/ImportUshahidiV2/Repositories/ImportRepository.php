<?php

/**
 * Import Repo
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\ImportUshahidiV2\Repositories;

use Ushahidi\App\ImportUshahidiV2\Import;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportRepository as ImportRepositoryContract;

class ImportRepository /*extends EloquentRepository*/ implements ImportRepositoryContract
{

    public function create(Import $model) : int
    {
        return $model->save() ? $model->id : false;
    }

    public function update(Import $model) : bool
    {
        return $model->save();
    }

    public function find(int $id) : ?Import
    {
        return Import::find($id);
    }
}
