<?php

/**
 * Ushahidi Platform Post Export Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository\Entity;

interface PostExportRepository
{

    /**
     * Get Column Names for given Post Data
     * @param  Post Data array $data
     * @return Array
     */
    public function retrieveMetaData($data, $attributes);

    public function retrieveCompletedStageNames($stage_ids);
}
