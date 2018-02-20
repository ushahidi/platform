<?php

/**
 * Ushahidi Post Data Export Job
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class PostDataExportJob extends StaticEntity
{
	protected $id;
	protected $postdataexport_id;
	protected $created;

	// StatefulData
	protected function getDerived()
	{
		// Foreign key alias
		return [
			'postdataexport_id' => ['postdataexport', 'postdataexport.id']
		];
	}


	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'       			=> 'int',
			'postdataexport' 	=> false,
			'postdataexport_id' => 'int',
			'created' 			=> 'int'
		];
	}

	// Entity
	public function getResource()
	{
		return 'post_data_export_job';
	}

	// StatefulData
	protected function getImmutable()
	{
		return array_merge(parent::getImmutable(), ['postdataexport_id']);
	}
}
