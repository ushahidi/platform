<?php

/**
 * Ushahidi Form Role
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class ContactPostState extends StaticEntity
{
	protected $post_id;
	protected $status;
	protected $contact_id;

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'form_id'  => 'int',
			'status'       => 'string',
			'contact_id'  => 'int'
		];
	}

	// Entity
	public function getResource()
	{
		return 'contact_post_state';
	}
}
