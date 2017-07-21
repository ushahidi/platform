<?php

/**
 * Repository for Tags
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

interface TranslationRepository
{
	public function saveTranslation($resource, $resourceId, $property, $translation, $source, $locale);

	public function getTranslations($resource, $resourceId);
}
