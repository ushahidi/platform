<?php

/**
 * Ushahidi Export Batch Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\ImportUshahidiV2;

use Ushahidi\Core\Entity\ImportMapping;

class ImportMappingRepository /*extends EloquentRepository*/ implements ImportMappingRepositoryContract
{

    public function create(array $input) : int
    {
        return 0;
    }
}
