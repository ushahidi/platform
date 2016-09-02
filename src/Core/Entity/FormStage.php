<?php

/**
 * Ushahidi Form Stage
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class FormStage extends StaticEntity
{
	protected $id;
	protected $form_id;
	protected $label;
	protected $priority;
	protected $icon;
	protected $required;

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'       => 'int',
			'form_id'  => 'int',
			'label'    => 'string',
			'priority' => 'int',
			'icon'     => 'string',
			'required' => 'boolean'
		];
	}

	// Entity
	public function getResource()
	{
		return 'form_stages';
	}
}
