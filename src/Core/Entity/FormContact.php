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
	protected $form_id;
	protected $contacts;
	// DataTransformer
	protected function getDefinition()
	{
		return [
			'form_id'            => 'int',
			//'user'          => false, /* alias */
			'contacts'       => 'string',
//			'data_provider' => 'string',
//			'type'          => 'string',
//			'contact'       => 'string',
//			'created'       => 'int',
//			'updated'       => 'int',
//			'can_notify'    => 'bool',
		];
	}

	// Entity
	public function getResource()
	{
		return 'form_contacts';
	}
}
