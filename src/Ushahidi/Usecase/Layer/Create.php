<?php

/**
 * Ushahidi Platform Admin Layer Create Use Case
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

class Create implements Usecase
{
	private $repo;
	private $valid;

	public function __construct(CreateLayerRepository $repo, Validator $valid, Authorizer $auth)
	{
		$this->repo  = $repo;
		$this->valid = $valid;
		$this->auth  = $auth;
	}

	public function interact(Data $input)
	{
		if (!$this->valid->check($input)) {
			throw new ValidatorException("Failed to validate layer", $this->valid->errors());
		}

		if (!$this->auth->isAllowed(new Layer, 'create')) {
			throw new AuthorizerException(sprintf(
				'User %s is not allowed to create layer',
				$this->auth->getUserId()
			));
		}

		$input = $input->asArray();

		return $this->repo->createLayer($input);
	}
}
