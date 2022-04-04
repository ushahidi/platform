<?php

/**
 * Ushahidi Platform Update Post Value Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository\Usecase;

interface UpdatePostValueRepository
{
    /**
     * Create new post value
     * @param  mixed   $value
     * @param  int     $form_attribute_id
     *
     * @param  int     $post_id
     */
    public function createValue($value, $form_attribute_id, $post_id);

    /**
     * Update an existing post value
     * @param  int     $id
     * @param  mixed   $value
     *
     * @param  void
     */
    public function updateValue($id, $value);


    /**
     * Delete values that are not in the ids array
     * @param  Integer $post_id
     * @param  array   $ids
     */
    public function deleteNotIn($post_id, array $ids);
}
