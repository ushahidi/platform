<?php

/**
 * Ushahidi Find Post Entity Trait
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\PostRepository;

trait FindPostEntity
{
	/**
	 * Find post entity based on
	 * @return Entity\Post
	 */
	protected function getEntity()
	{
		$this->verifyPostRepo($this->repo);

		$id        = $this->getIdentifier('id');
		$parent_id = $this->getIdentifier('parent_id');
		$locale    = $this->getIdentifier('locale');
		$type      = $this->getIdentifier('type');

		if ($parent_id && $locale) {
			$entity = $this->repo->getByLocale($locale, $parent_id, $type);
		} else {
			// Load post by id and parent id, because if its a revision or update
			// we should only return revision for the particular parent post
			$entity = $this->repo->getByIdAndParent($id, $parent_id, $type);
		}

		$this->verifyEntityLoaded($entity, compact('id', 'parent_id', 'locale', 'type'));

		return $entity;
	}

	/**
	 * Use type hinting to ensure that the argument is a PostRepository.
	 * @param  PostRepository $repo
	 * @return boolean
	 */
	protected function verifyPostRepo(PostRepository $repo)
	{
		return true;
	}

	// VerifyEntityLoaded
	abstract protected function verifyEntityLoaded(Entity $entity, $lookup);

	// IdentifyRecords
	abstract protected function getIdentifier($name, $default = null);
}
