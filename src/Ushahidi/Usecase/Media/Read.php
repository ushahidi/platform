<?php

/**
 * Ushahidi Platform Admin Media Read Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Media;

use Ushahidi\Usecase;
use Ushahidi\Data;
use Ushahidi\Tool\Authorizer;
use Ushahidi\Exception\AuthorizerException;
use Ushahidi\Exception\NotFoundException;

class Read implements Usecase
{
	private $repo;
	private $valid;
	private $auth;

	public function __construct(
		ReadMediaRepository $repo,
		Authorizer $auth
	) {
		$this->repo  = $repo;
		$this->auth  = $auth;
	}

	public function interact(Data $input)
	{
		$media = $this->repo->get($input->id);

		if (!$media->id) {
			throw new NotFoundException(sprintf(
				'Media %d does not exist',
				$input->id
			));
		}

		if (!$this->auth->isAllowed($media, 'get')) {
			throw new AuthorizerException(sprintf(
				'User %s is not allowed to read media %s',
				$this->auth->getUserId(),
				$input->id
			));
		}

		return $media;
	}
}
