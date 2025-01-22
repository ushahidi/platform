<?php

/**
 * Ushahidi Verify Entity Loaded Trait
 *
 * Gives objects one new method:
 * `verifyEntityLoaded(Entity $entity)`
 *
 * Triggers a NotFoundException if it's not.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Concerns;

use Ushahidi\Contracts\Entity;
use Ushahidi\Core\Exception\NotFoundException;

trait VerifyEntityLoaded
{
    /**
     * Verifies that a given entity has been loaded, by checking that the "id"
     * property is not empty.
     * @param  \Ushahidi\Contracts\Entity $entity
     * @param  mixed $lookup
     * @return \Ushahidi\Contracts\Entity
     * @throws \Ushahidi\Core\Exception\NotFoundException
     */
    protected function verifyEntityLoaded(Entity $entity, $lookup)
    {
        if (!$entity->getId()) {
            if (is_array($lookup)) {
                $arr = [];
                foreach ($lookup as $key => $val) {
                    $arr[] = "$key: $val";
                }
                $lookup_string = implode(', ', $arr);
            } else {
                $lookup_string = $lookup;
            }

            throw new NotFoundException(sprintf(
                'Could not locate any %s matching [%s]',
                $entity->getResource(),
                $lookup_string
            ));
        }

        return $entity;
    }
}
