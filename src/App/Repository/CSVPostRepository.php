<?php

/**
 * Ushahidi Post Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository;

class CSVPostRepository extends PostRepository
{
    protected function getPostValues($id, $excludePrivateValues, $excludeStages)
    {

        // Get all the values for the post. These are the EAV values.
        $values = $this->post_value_factory
            ->proxy($this->include_value_types)
            ->getAllForPost($id, $this->include_attributes, $excludeStages, $excludePrivateValues);

        $output = [];
        foreach ($values as $value) {
            if (empty($output[$value->key])) {
                $output[$value->key] = [];
            }
            if (is_array($value->value) && isset($value->value['o_filename'])) {
                $output[$value->key][] = $value->value['o_filename'];
            } elseif ($value->value !== null) {
                $output[$value->key][] = $value->value;
            }
        }
        return $output;
    }
}
