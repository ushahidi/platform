<?php

/**
 * Ushahidi Platform Update Message Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Message;

use Ushahidi\Core\Data;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Usecase\UpdateUsecase;

class UpdateMessage extends UpdateUsecase
{
	// UpdateUsecase
	protected function beforeValidate(Entity $entity, Data $input)
	{
		$this->valid->setDirection($entity->direction);
	}
}
