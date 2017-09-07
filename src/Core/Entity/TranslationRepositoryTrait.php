<?php

/**
 * Formatter extensions to get entity translations
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

trait TranslationRepositoryTrait
{

	/**
	 * @var TranslationRepository
	 */
	protected $translationRepo;

	/**
	 * Inject a repository that can create entities.
	 *
	 * @param  $repo CreateRepository
	 * @return $this
	 */
	public function setTranslationRepository(TranslationRepository $translationRepo)
	{
		$this->translationRepo = $translationRepo;

		return $this;
	}
}
