<?php

/**
 * Ushahidi Platform Post Search Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\SearchData;
use Ushahidi\Core\Traits\Data\SortableData;

class PostSearchData extends SearchData
{
	use SortableData;

	public $q; // LIKE title OR content
	public $id;
	public $slug;
	public $parent;
	public $type;
	public $form;
	public $user;
	public $locale;
	public $status;
	public $created_after;
	public $created_before;
	public $updated_after;
	public $updated_before;
	public $bbox;
	public $tags;
	public $values;
	public $set;

	// center_point & within_km are interdependent
	public $center_point;
	public $within_km;

	public $include_types;
	public $include_attributes;
}
