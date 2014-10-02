<?php

/**
 * Ushahidi Platform Layer Read Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Layer;

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
		ReadLayerRepository $repo,
		Authorizer $auth
	) {
		$this->repo  = $repo;
		$this->auth  = $auth;
	}

	public function interact(Data $input)
	{
		$layer = $this->repo->get($input->id);

		if (!$layer->id) {
			throw new NotFoundException(sprintf(
				'Layer %d does not exist',
				$input->id
			));
		}

		if (!$this->auth->isAllowed($layer, 'get')) {
			throw new AuthorizerException(sprintf(
				'User %s is not allowed to read layer %s',
				$this->auth->getUserId(),
				$input->id
			));
		}

		return $layer;
	}
}
