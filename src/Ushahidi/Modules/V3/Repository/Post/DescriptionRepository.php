<?php

/**
 * Ushahidi Post Varchar Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Repository\Post;

class DescriptionRepository extends TextRepository
{
    public function getAllForPost(
        $post_id,
        array $include_attributes = [],
        array $exclude_stages = [],
        $excludePrivateValues = true
    ) {
        return [];
    }
  // DeleteRepository
  // This value should be immutable and unchangeable
    public function createValue($value, $form_attribute_id, $post_id)
    {
        return 0;
    }

    public function createManyValues(array $values, int $form_attribute_id)
    {
        return 0;
    }

    public function updateValue($id, $value)
    {
        return 0;
    }
}
