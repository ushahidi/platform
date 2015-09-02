<?php

/**
 * Ushahidi Platform Utilize Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2015 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\DataProvider;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Usecase\UpdateUsecase;

class UpdateDataProvider extends UpdateUsecase
{
	protected function verifyUpdateAuth(Entity $entity)
	{

		// feature
		$entity_values = $entity->getDefinition();

		// if not enabled
		if (!feature($entity_values['name']))
		{
			throw HTTP_Exception::factory(403, 'This feature is not enabled for your deployment');
		}

	}
}
