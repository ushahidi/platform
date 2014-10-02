<?php

/**
 * Ushahidi Platform Layer Search Use Case
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
use Ushahidi\Tool\Validator;
use Ushahidi\Exception\AuthorizerException;
use Ushahidi\Exception\ValidatorException;

class Search implements Usecase
{
	private $repo;
	private $auth;

	public function __construct(
		SearchLayerRepository $repo,
		Authorizer $auth
	) {
		$this->repo  = $repo;
		$this->auth  = $auth;
	}

	public function interact(Data $input)
	{
		$layers = $this->repo->search($input);

		foreach ($layers as $idx => $layer) {
			if (!$this->auth->isAllowed($layer, 'get')) {
				unset($layers[$idx]);
			}
		}

		return $layers;
	}
}
