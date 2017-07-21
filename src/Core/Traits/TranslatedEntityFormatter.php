<?php

/**
 * Formatter extensions to get entity translations
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\TranslationRepositoryTrait;

trait TranslatedEntityFormatter
{
	use TranslationRepositoryTrait;

	protected function getEntityTranslations(Entity $entity)
	{
		return $this->translationRepo->getTranslations($entity->getResource(), $entity->getId());
	}
}
