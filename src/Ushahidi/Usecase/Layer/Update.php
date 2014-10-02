<?php

/**
 * Ushahidi Platform Layer Update Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Layer;

use Ushahidi\Usecase;
use Ushahidi\Data;
use Ushahidi\Entity\Layer;
use Ushahidi\Tool\Authorizer;
use Ushahidi\Tool\Validator;
use Ushahidi\Exception\AuthorizerException;
use Ushahidi\Exception\ValidatorException;
use Ushahidi\Exception\NotFoundException;

class Update implements Usecase
{
	private $repo;
	private $valid;

	private $updated = [];

	public function __construct(UpdateLayerRepository $repo, Validator $valid, Authorizer $auth)
	{
		$this->repo  = $repo;
		$this->valid = $valid;
		$this->auth = $auth;
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

		// We only want to work with values that have been changed
		$update = $input->getDifferent($layer->asArray());

		if (!$this->valid->check($update)) {
			throw new ValidatorException("Failed to validate layer", $this->valid->errors());
		}

		if (!$this->auth->isAllowed($layer, 'update')) {
			throw new AuthorizerException(sprintf(
				'User %s is not allowed to update layer %s',
				$this->auth->getUserId(),
				$input->id
			));
		}

		// Determine what changes to make in the layer
		$this->updated = $update->asArray();

		$this->repo->updateLayer($layer->id, $this->updated);

		// Reflect the changes in the layer
		$layer->setData($this->updated);

		return $layer;
	}

	public function getUpdated()
	{
		return $this->updated;
	}
}
