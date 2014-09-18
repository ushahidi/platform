<?php

/**
 * Ushahidi Platform Admin Post Update Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Post;

use Ushahidi\Entity\Post;
use Ushahidi\Tool\Validator;
use Ushahidi\Tool\Authorizer;
use Ushahidi\Exception\AuthorizerException;
use Ushahidi\Exception\ValidatorException;
use Ushahidi\Exception\NotFoundException;

class Update
{
	private $repo;
	private $valid;

	private $updated = [];

	public function __construct(UpdatePostRepository $repo, Validator $valid, Authorizer $auth)
	{
		$this->repo  = $repo;
		$this->valid = $valid;
		$this->auth  = $auth;
	}

	public function interact(PostData $input)
	{
		$post = $this->repo->getByIdAndParent($input->id, $input->parent_id);

		if (!$post->id) {
			throw new NotFoundException(sprintf(
				'Post %d does not exist',
				$input->id
			));
		}

		// We only want to work with values that have been changed
		$update = $input->getDifferent($post->asArray());

		// Include type for use in validation
		// These are never updated, but needed for some checks
		// @todo figure out a better way to include these
		$update->type = $post->type;

		if (!$this->valid->check($update)) {
			throw new ValidatorException("Failed to validate post", $this->valid->errors());
		}

		// Access checks
		if (! $this->auth->isAllowed($post, 'update')) {
			throw new AuthorizerException(sprintf(
				'User %s is not allowed to update post %s',
				$this->auth->getUserId(),
				$post->id
			));
		}

		// if changing user id or author info
		if (isset($update->user_id) || isset($update->author_email) || isset($update->author_realname)) {
			if (! $this->auth->isAllowed($post, 'change_user')) {
				throw new AuthorizerException(sprintf(
					'User %s is not allowed to update user details for post %s',
					$this->auth->getUserId(),
					$post->id
				));
			}
		}

		// Determine what changes to make in the post
		// @todo figure out why is $update `PostData` till here?
		// either it should stay `PostData`, or it should start as an array.
		$this->updated = $update->asArray();

		// Update the post and get the updated post back
		$post = $this->repo->updatePost($post->id, $this->updated);

		// @todo make tag/values entites arrays before they go into Post
		// and make sure $post->setData($updated) actually works.

		return $post;
	}

	public function getUpdated()
	{
		return $this->updated;
	}
}
