<?php

/**
 * Ushahidi Contact Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

use Ushahidi\Entity;

class Contact extends Entity
{
	public $id;
	public $user_id;
	public $data_provider;
	public $type;
	public $contact;
	public $created;

	// Entity
	public function getResource()
	{
		return 'contacts';
	}

	// Entity
	public function getId()
	{
		return $this->id;
	}
}
