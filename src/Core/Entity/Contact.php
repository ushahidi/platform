<?php

/**
 * Ushahidi Contact Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class Contact extends StaticEntity
{
	// Valid contact types
	const EMAIL    = 'email';
	const PHONE    = 'phone';
	const TWITTER  = 'twitter';

	protected $id;
	protected $user_id;
	protected $data_provider;
	protected $type;
	protected $contact;
	protected $created;
	protected $updated;
	protected $can_notify;

	// StatefulData
	protected function getDerived()
	{
		// Foreign key alias
		return [
			'user_id' => ['user', 'user.id']
		];
	}

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'            => 'int',
			'user'          => false, /* alias */
			'user_id'       => 'int',
			'data_provider' => 'string',
			'type'          => 'string',
			'contact'       => 'string',
			'created'       => 'int',
			'updated'       => 'int',
			'can_notify'    => 'bool',
		];
	}

	// Entity
	public function getResource()
	{
		return 'contacts';
	}
}
