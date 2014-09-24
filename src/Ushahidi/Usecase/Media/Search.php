<?php

/**
 * Ushahidi Platform Media Search Use Case
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
use Ushahidi\Tool\Validator;
use Ushahidi\Exception\AuthorizerException;
use Ushahidi\Exception\ValidatorException;

class Search implements Usecase
{
	private $repo;
	private $auth;

	public function __construct(
		SearchMediaRepository $repo,
		Authorizer $auth
	) {
		$this->repo  = $repo;
		$this->auth  = $auth;
	}

	public function interact(Data $input)
	{
		$results = $this->repo
			->setSearchParams($input, $input->getSortingParams())
			->getSearchResults();

		foreach ($results as $idx => $media) {
			if (!$this->auth->isAllowed($media, 'get')) {
				unset($results[$idx]);
			}
		}

		return $results;
	}
}
