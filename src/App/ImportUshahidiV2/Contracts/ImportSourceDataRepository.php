<?php

/**
 * Import Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2020 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\ImportUshahidiV2\Contracts;

use Ushahidi\App\ImportUshahidiV2\ImportSourceData;

interface ImportSourceDataRepository
{
    public function create(ImportSourceData $model) : int;
}
