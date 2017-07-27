<?php

/**
 * Saves Translations Trait
 *
 *
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Concerns;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\TranslationRepositoryTrait;

trait SavesTranslations
{
	use TranslationRepositoryTrait;

	protected function saveTranslations(Entity $entity) {
		if (($translations = $this->getPayload('translations', false)) &&
		    ($properties = $entity->getTranslatable())
		) {
			foreach ($translations as $locale => $values) {
				foreach ($properties as $property) {
					if (isset($values[$property])) {
						$this->translationRepo->saveTranslation($entity->getResource(), $entity->getId(), $property, $values[$property], (string)$entity->$property, $locale);
					}
				}
			}
		}
	}

}
