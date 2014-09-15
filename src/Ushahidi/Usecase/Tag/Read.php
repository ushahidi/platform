<?php

/**
 * Ushahidi Platform Admin Tag Read Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Tag;

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
		ReadTagRepository $repo,
		Authorizer $auth
	) {
		$this->repo  = $repo;
		$this->auth  = $auth;
	}

	public function interact(Data $input)
	{
		$tag = $this->repo->get($input->id);

		if (!$tag->id) {
			throw new NotFoundException(sprintf(
				'Tag %d does not exist',
				$input->id
			));
		}

		if (!$this->auth->isAllowed($tag, 'get')) {
			throw new AuthorizerException(sprintf(
				'User %s is not allowed to read tag %s',
				$this->auth->getUserId(),
				$input->id
			));
		}

		return $tag;
	}
}
