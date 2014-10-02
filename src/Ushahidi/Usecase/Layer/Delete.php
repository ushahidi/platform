<?php

/**
 * Ushahidi Platform Layer Delete Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Layer;

use Ushahidi\Data;
use Ushahidi\Usecase;
use Ushahidi\Entity\Layer;
use Ushahidi\Tool\Authorizer;
use Ushahidi\Exception\AuthorizerException;
use Ushahidi\Exception\NotFoundException;

class Delete implements Usecase
{
	private $repo;
	private $auth;

	public function __construct(DeleteLayerRepository $repo, Authorizer $auth)
	{
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

		if (!$this->auth->isAllowed($layer, 'delete')) {
			throw new AuthorizerException(sprintf(
				'User %s is not allowed to delete layer %s',
				$this->auth->getUserId(),
				$input->id
			));
		}

		$this->repo->deleteLayer($layer->id);

		return $layer;
	}
}
