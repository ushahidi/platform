<?php

/**
 * Ushahidi Platform Admin Tag Search Use Case
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
use Ushahidi\Tool\Validator;
use Ushahidi\Exception\AuthorizerException;
use Ushahidi\Exception\ValidatorException;

class Search implements Usecase
{
	private $repo;
	private $auth;

	public function __construct(
		SearchTagRepository $repo,
		Authorizer $auth
	) {
		$this->repo  = $repo;
		$this->auth  = $auth;
	}

	public function interact(Data $input)
	{
		$tags = $this->repo->search($input);

		foreach ($tags as $idx => $tag) {
			if (!$this->auth->isAllowed($tag, 'get')) {
				unset($tags[$idx]);
			}
		}

		return $tags;
	}
}
