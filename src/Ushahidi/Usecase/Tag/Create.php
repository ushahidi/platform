<?php

/**
 * Ushahidi Platform Admin Tag Create Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Tag;

use Ushahidi\Entity\Tag;
use Ushahidi\Tool\Validator;
use Ushahidi\Exception\ValidatorException;

class Create
{
	private $repo;
	private $valid;

	public function __construct(CreateTagRepository $repo, Validator $valid)
	{
		$this->repo  = $repo;
		$this->valid = $valid;
	}

	public function interact(Array $input)
	{
		if (!$this->valid->check($input))
			throw new ValidatorException("Failed to validate tag", $this->valid->errors());

		$this->repo->createTag($input);

		$input['id']      = $this->repo->getCreatedTagId();
		$input['created'] = $this->repo->getCreatedTagTimestamp();

		return new Tag($input);
	}
}

