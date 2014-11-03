<?php

/**
 * Ushahidi Platform Layer Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Layer;

use Ushahidi\Core\Data;

class LayerData extends Data
{
	public $id;
	public $name;
	public $data_url;
	public $media_id;
	public $type;
	public $options;
	public $active;
	public $visible_by_default;
}
