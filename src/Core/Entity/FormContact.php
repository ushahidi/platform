<?php

/**
 * Ushahidi Form Contact
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class FormContact extends StaticEntity
{
	protected $id;
	protected $form_id;
	protected $contact_id;

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'       => 'int',
			'form_id'  => 'int',
			'contact_id'  => 'int'
		];
	}

	// Entity
	public function getResource()
	{
		return 'form_contacts';
	}
}
