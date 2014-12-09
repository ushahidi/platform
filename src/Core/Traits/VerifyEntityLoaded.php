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

namespace Ushahidi\Core\Traits;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Exception\NotFoundException;

trait VerifyEntityLoaded
{
	/**
	 * Verifies that a given entity has been loaded, by checking that the "id"
	 * property is not empty.
	 * @param  Entity  $entity
	 * @param  Integer $lookup_id
	 * @return void
	 * @throws NotFoundException
	 */
	private function verifyEntityLoaded(Entity $entity, $lookup_id)
	{
		if (!$entity->getId()) {
			throw new NotFoundException(sprintf(
				'Could not locate resource %s: %s',
				$entity->getResource(),
				$lookup_id
			));
		}
	}
}
