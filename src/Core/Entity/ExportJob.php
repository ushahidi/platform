<?php

/**
 * Ushahidi Export Job
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class ExportJob extends StaticEntity
{
	protected $id;
	protected $entity_type;
	protected $user_id;
	protected $fields;
	protected $filters;
	protected $status;
	protected $url;
	protected $created;
	protected $updated;

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
			'id'       			=> 'int',
			'entity_type'     	=> 'string',
			'user_id'			=> 'int',
			'status'     		=> 'string',
			'url'		     	=> 'string',
			'fields'    	    => '*json',
			'filters'   	    => '*json',
			'created' 			=> 'int',
			'updated' 			=> 'int'
		];
	}

	// Entity
	public function getResource()
	{
		return 'export_job';
	}

	// StatefulData
	protected function getImmutable()
	{
		return array_merge(parent::getImmutable(), ['user_id']);
	}
}
