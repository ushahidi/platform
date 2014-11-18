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

use Ushahidi\Core\Entity;

// The layer entity is used to store map overlays
class Layer extends Entity
{
	public $id;
	// A layer needs a name so we can list it in the layer control
	public $name;
	// An overlay can be loaded from a remote URL..
	public $data_url;
	// Or uploaded via the media API
	public $media_id;
	// We support different layer types, such as GeoJSON, WMS, tile
	public $type;
	// Layer formats like WMS can accept an array of extra options
	public $options;
	// Only active layers are added to the map
	public $active;
	// Layers can visible on the map by default, or only shown when a user enables them
	public $visible_by_default;
	public $created;
	public $updated;

	/* Entity */
	public function getResource()
	{
		return 'layer';
	}
}
