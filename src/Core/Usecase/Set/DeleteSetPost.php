<?php

/**
 * Remove post from set Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Set;

use Ushahidi\Core\Usecase\DeleteUsecase;
use Ushahidi\Core\Data;
use Ushahidi\Core\Tool\ValidatorTrait;
use Ushahidi_Repository;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\SetRepository;

class DeleteSetPost extends DeleteUsecase
{
	use SetRepositoryTrait,
		GetSet,
		AuthorizeSet;

	// Usecase
	public function interact()
	{
		// Fetch the post, using provided identifiers...
		$post = $this->getEntity();

		// ... fetch the set entity
		$set = $this->getSetEntity();

		// ... and that the set can be edited by the current user
		$this->verifySetUpdateAuth($set);

		// ... remove the post from the set
		$this->setRepo->deleteSetPost($set->id, $post->id);

		// ... and return the formatted entity
		return $this->formatter->__invoke($post);
	}
}
