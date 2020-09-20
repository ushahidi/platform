<?php

/**
 * Import Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\ImportUshahidiV2\Contracts;

use Ushahidi\App\ImportUshahidiV2\Import;

interface ImportRepository
{
    public function create(Import $model) : int;

    public function update(Import $model) : bool;

    public function find(int $id) : ?Import;
}
