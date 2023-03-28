<?php

/**
 * Repository for Tags
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\Repository\EntityCreate;
use Ushahidi\Contracts\Repository\EntityCreateMany;
use Ushahidi\Contracts\Repository\EntityGet;
use Ushahidi\Contracts\Repository\EntityExists;

interface TagRepository extends
    EntityGet,
    EntityCreate,
    EntityCreateMany,
    EntityExists
{

    /**
     * @param  string $slug
     *
     * @return boolean
     */
    public function isSlugAvailable($slug);

    /**
     * @param string $tag
     *
     * @return \Ushahidi\Core\Entity\Tag
     */
    public function getByTag($tag);

    /**
     * @param int|string|\Ushahidi\Core\Entity\Tag $value
     *
     * @return boolean
     */
    public function doesTagExist($value);
}
