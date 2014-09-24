<?php

/**
 * Ushahidi Platform Admin Media Create Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Media;

use Ushahidi\Usecase;
use Ushahidi\Data;
use Ushahidi\Entity\Media;
use Ushahidi\Tool\Authorizer;
use Ushahidi\Tool\Validator;
use Ushahidi\Exception\AuthorizerException;
use Ushahidi\Exception\ValidatorException;

class Create implements Usecase
{
	private $repo;
	private $valid;

	public function __construct(CreateMediaRepository $repo, Validator $valid, Authorizer $auth)
	{
		$this->repo  = $repo;
		$this->valid = $valid;
		$this->auth  = $auth;
	}

	public function interact(Data $input)
	{
		if (!$this->valid->check($input)) {
			throw new ValidatorException('Failed to validate media file', $this->valid->errors());
		}

		if (!$this->auth->isAllowed(new Media, 'post')) {
			throw new AuthorizerException(sprintf(
				'User %d is not allowed to create media',
				$this->auth->getUserId()
			));
		}

		$this->repo->createMedia(
			$input->file,
			$input->caption,
			$this->auth->getUserId()
		);

		$media = $this->repo->getCreatedMedia();

		if (!$this->auth->isAllowed($media, 'get')) {
			throw new AuthorizerException(sprintf(
				'User %d is not allowed to read media %d',
				$this->auth->getUserId(),
				$media->id
			));
		}

		return $media;
	}
}
