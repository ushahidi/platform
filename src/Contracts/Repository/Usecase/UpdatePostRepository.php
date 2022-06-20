<?php

/**
 * Ushahidi Platform Update Post Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository\Usecase;

interface UpdatePostRepository
{
    /**
     * @param  string $slug
     *
     * @return boolean
     */
    public function isSlugAvailable($slug);

    /**
     * @param  string $locale
     * @param  int    $parent_id
     * @param  string $type
     *
     * @return boolean
     */
    public function doesTranslationExist($locale, $parent_id, $type);

    /**
     * Checking if a post requires approval via the form it belongs too
     *
     * @param int|null $formId
     * @return boolean
     */
    public function doesPostRequireApproval($formId);

    /**
     * Get total number of published posts
     * @return int
     */
    public function getPublishedTotal();
}
