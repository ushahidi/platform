<?php

/**
 * Ushahidi Platform Sortable Data Trait
 *
 * Defines "orderby", "order", "limit", and "offset" properties in an object.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits\Data;

trait SortableData
{
	public $orderby;
	public $order;
	public $limit;
	public $offset;

	public function getSortingParams()
	{
		return [
			'orderby' => $this->orderby,
			'order'   => $this->order,
			'limit'   => $this->limit,
			'offset'  => $this->offset,
			];
	}
}
