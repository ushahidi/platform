<?php

/**
 * Ushahidi Platform Admin Media Delete Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Media;

use Ushahidi\Entity\Media;
use Ushahidi\Tool\Authorizer;
use Ushahidi\Tool\Validator;
use Ushahidi\Exception\AuthorizerException;
use Ushahidi\Exception\ValidatorException;

class Delete
{
	private $repo;
	private $valid;

	public function __construct(DeleteMediaRepository $repo, Validator $valid, Authorizer $auth)
	{
		$this->repo  = $repo;
		$this->valid = $valid;
		$this->auth  = $auth;
	}

	public function interact(MediaDeleteData $input)
	{
		if (!$this->valid->check($input))
			throw new ValidatorException('Failed to validate media delete', $this->valid->errors());

		$media = $this->repo->get($input->id);

		if (!$this->auth->isAllowed($media, 'delete', $input->user_id))
			throw new AuthorizerException(sprintf('User %s is not allowed to delete media file %s',
				$input->user_id,
				$input->id
				));

		$this->repo->deleteMedia($input->id, $input->user_id);

		return $media;
	}
}

