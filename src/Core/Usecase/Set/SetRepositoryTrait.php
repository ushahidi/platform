<?php

/**
 * Set Repository Entity Trait
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Set;

use Ushahidi\Core\Entity\SetRepository;

trait SetRepositoryTrait
{
	protected $setRepo;

	public function setSetRepository(SetRepository $setRepo)
	{
		$this->setRepo = $setRepo;
		return $this;
	}

	public function getSetRepository()
	{
		return $this->setRepo;
	}
}
