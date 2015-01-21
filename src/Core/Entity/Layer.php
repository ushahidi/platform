<?php

/**
 * Ushahidi Layer Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

// The layer entity is used to store map overlays
class Layer extends StaticEntity
{
	protected $id;
	// A layer needs a name so we can list it in the layer control
	protected $name;
	// An overlay can be loaded from a remote URL..
	protected $data_url;
	// Or uploaded via the media API
	protected $media_id;
	// We support different layer types, such as GeoJSON, WMS, tile
	protected $type;
	// Layer formats like WMS can accept an array of extra options
	protected $options;
	// Only active layers are added to the map
	protected $active;
	// Layers can visible on the map by default, or only shown when a user enables them
	protected $visible_by_default;
	protected $created;
	protected $updated;

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'                 => 'int',
			'media_id'           => 'int',
			'name'               => 'string',
			'data_url'           => 'string',
			'type'               => 'string',
			'options'            => '*json',
			'active'             => 'bool',
			'visible_by_default' => 'bool',
			'created'            => 'int',
			'updated'            => 'int',
		];
	}

	// Entity
	public function getResource()
	{
		return 'layer';
	}
}
