<?php

/**
 * Ushahidi Platform Admin Media Create Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Media;

use Ushahidi\Entity\Media;
use Ushahidi\Tool\Validator;
use Ushahidi\Exception\ValidatorException;

class Create
{
	private $repo;
	private $valid;

	public function __construct(CreateMediaRepository $repo, Validator $valid)
	{
		$this->repo  = $repo;
		$this->valid = $valid;
	}

	public function interact(MediaData $input)
	{
		if (!$this->valid->check($input))
			throw new ValidatorException("Failed to validate media file", $this->valid->errors());

		$this->repo->createMedia(
			$input->file,
			$input->caption,
			$input->user_id
			);

		return $this->repo->getCreatedMedia();
	}
}


